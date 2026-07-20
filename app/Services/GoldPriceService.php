<?php

namespace App\Services;

use App\Models\GoldPrice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoldPriceService
{
    /**
     * Ambil harga emas hari ini dari DB (dengan cache 6 jam).
     * Jika tidak ada, kembalikan harga terakhir yang tersedia.
     */
    public function getTodayPrice(): ?GoldPrice
    {
        $cached = Cache::get('gold_price_today');

        // Jika cache berisi data rusak (misal sisa PHP versi lama → __PHP_Incomplete_Class),
        // hapus cache lalu ambil langsung dari DB.
        if ($cached !== null && !($cached instanceof GoldPrice)) {
            Cache::forget('gold_price_today');
            Cache::forget('usd_idr_rate');
            $cached = null;
        }

        if ($cached !== null) {
            return $cached;
        }

        $fresh = GoldPrice::where('price_date', today()->toDateString())->first()
            ?? GoldPrice::latest('price_date')->first();

        if ($fresh) {
            Cache::put('gold_price_today', $fresh, now()->addHours(6));
        }

        return $fresh;
    }

    /**
     * Hapus cache harga hari ini (dipanggil saat admin update harga).
     */
    public function clearCache(): void
    {
        Cache::forget('gold_price_today');
    }

    /**
     * Fetch harga emas dari API eksternal GRATIS:
     *  1. Ambil harga XAU/USD dari metals.live (no key needed)
     *  2. Ambil kurs USD/IDR dari open.er-api.com (no key needed)
     *  3. Fallback: goldapi.io jika ada API key di .env
     *  4. Fallback terakhir: harga terakhir dari DB
     *
     * @return array{buy: float, sell: float, source: string}|null
     */
    public function fetchFromExternal(): ?array
    {
        // === STRATEGI 1: metals.live (free, no key) ===
        try {
            $metalsRes = Http::timeout(10)->get('https://metals.live/api/spot');
            if ($metalsRes->successful()) {
                $metals = $metalsRes->json();
                // metals.live mengembalikan array [{metal:'gold', price: ...}, ...]
                $xauUsd = null;
                if (is_array($metals)) {
                    foreach ($metals as $item) {
                        if (isset($item['metal']) && strtolower($item['metal']) === 'gold') {
                            $xauUsd = (float) $item['price'];
                            break;
                        }
                    }
                }

                if ($xauUsd) {
                    $idrRate = $this->getUsdToIdr();
                    if ($idrRate) {
                        return $this->calculatePrices($xauUsd, $idrRate, 'metals.live');
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('GoldPriceService: metals.live gagal: ' . $e->getMessage());
        }

        // === STRATEGI 2: goldapi.io (jika ada API key di .env) ===
        $apiKey = config('services.goldapi.key');
        if ($apiKey) {
            try {
                $response = Http::withHeaders([
                    'x-access-token' => $apiKey,
                    'Content-Type'   => 'application/json',
                ])->timeout(10)->get('https://www.goldapi.io/api/XAU/IDR');

                if ($response->successful()) {
                    $data    = $response->json();
                    $priceOz = $data['price'] ?? null;
                    if ($priceOz) {
                        $priceGram = $priceOz / 31.1035;
                        $buy       = round($priceGram * 0.97, -3);
                        $sell      = round($priceGram, -3);
                        return ['buy' => $buy, 'sell' => $sell, 'source' => 'goldapi.io'];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('GoldPriceService: goldapi.io gagal: ' . $e->getMessage());
            }
        }

        // === STRATEGI 3: Fetch XAU dari alternative endpoint + kurs IDR ===
        try {
            // Coba dari frankfurter.app — mereka support XAU sebagai mata uang
            $fxRes = Http::timeout(10)->get('https://api.frankfurter.app/latest', [
                'from'   => 'XAU',
                'to'     => 'USD',
            ]);
            if ($fxRes->successful()) {
                $fxData = $fxRes->json();
                $xauToUsd = $fxData['rates']['USD'] ?? null;
                if ($xauToUsd) {
                    $idrRate = $this->getUsdToIdr();
                    if ($idrRate) {
                        // xauToUsd = berapa USD per 1 XAU (troy oz)
                        return $this->calculatePrices($xauToUsd, $idrRate, 'frankfurter.app');
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('GoldPriceService: frankfurter.app gagal: ' . $e->getMessage());
        }

        // === FALLBACK: Gunakan harga terakhir dari DB ===
        $last = GoldPrice::latest('price_date')->first();
        if ($last) {
            return [
                'buy'    => (float) $last->buy_price_per_gram,
                'sell'   => (float) $last->sell_price_per_gram,
                'source' => 'Harga Sebelumnya (Fallback)',
            ];
        }

        return null;
    }

    /**
     * Ambil kurs USD → IDR dari open.er-api.com (gratis, tanpa key).
     */
    private function getUsdToIdr(): ?float
    {
        return Cache::remember('usd_idr_rate', now()->addHours(12), function () {
            try {
                $res = Http::timeout(10)->get('https://open.er-api.com/v6/latest/USD');
                if ($res->successful()) {
                    return (float) ($res->json()['rates']['IDR'] ?? null);
                }
            } catch (\Exception $e) {
                Log::warning('GoldPriceService: USD/IDR gagal: ' . $e->getMessage());
            }
            return null;
        });
    }

    /**
     * Hitung harga beli/jual dari XAU/USD + kurs IDR.
     * Spread: sell ~1.5% di atas mid, buy ~1.5% di bawah mid.
     */
    private function calculatePrices(float $xauUsd, float $idrRate, string $source): array
    {
        $pricePerOzIdr  = $xauUsd * $idrRate;
        $pricePerGramIdr = $pricePerOzIdr / 31.1035;

        // Toko jual ke pelanggan (harga jual lebih tinggi)
        $sell = round($pricePerGramIdr * 1.015, -3);
        // Toko beli dari pelanggan (harga beli lebih rendah)
        $buy  = round($pricePerGramIdr * 0.985, -3);

        return [
            'buy'    => $buy,
            'sell'   => $sell,
            'source' => $source . ' (XAU/USD→IDR)',
        ];
    }
}
