<?php

namespace Database\Seeders;

use App\Models\GoldPrice;
use App\Models\User;
use App\Services\GoldPriceService;
use Illuminate\Database\Seeder;

class GoldPriceSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (! $admin) return;

        // Ambil harga emas murni dari API eksternal asli via GoldPriceService
        $goldService = app(GoldPriceService::class);
        $fetched = $goldService->fetchFromExternal();

        $baseBuy  = $fetched['buy'] ?? 1_580_000;
        $baseSell = $fetched['sell'] ?? 1_620_000;
        $apiSource = $fetched['source'] ?? 'API Eksternal';

        // Buat data 30 hari terakhir dengan fluktuasi realisitik berbasis harga API
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();

            if ($i === 0) {
                // Hari ini paksa gunakan harga pas persis dari API eksternal
                $buy = $baseBuy;
                $sell = $baseSell;
                $source = $apiSource;
            } else {
                // Fluktuasi ±1.5% untuk histori
                $fluctuation = (rand(-150, 150) / 10000);
                $buy  = round($baseBuy  * (1 + $fluctuation), -3);
                $sell = round($baseSell * (1 + $fluctuation), -3);
                $source = $apiSource . ' (Histori)';
            }

            $existing = GoldPrice::whereDate('price_date', $date)->first();
            $data = [
                'buy_price_per_gram'  => $buy,
                'sell_price_per_gram' => $sell,
                'source'              => $source,
                'recorded_by'         => $admin->id,
                'notes'               => $i === 0 ? 'Harga live real-time dari API eksternal.' : 'Data histori berbasis API eksternal.',
            ];

            if ($existing) {
                $existing->update($data);
            } else {
                GoldPrice::create(array_merge(['price_date' => $date], $data));
            }
        }

        $goldService->clearCache();
    }
}
