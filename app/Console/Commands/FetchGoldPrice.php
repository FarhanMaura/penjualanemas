<?php

namespace App\Console\Commands;

use App\Models\GoldPrice;
use App\Models\User;
use App\Services\GoldPriceService;
use Illuminate\Console\Command;

class FetchGoldPrice extends Command
{
    protected $signature   = 'gold:fetch';
    protected $description = 'Ambil harga emas hari ini dari API eksternal dan simpan ke database.';

    public function __construct(private GoldPriceService $service)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Mengambil harga emas dari sumber eksternal...');

        $data = $this->service->fetchFromExternal();

        if (! $data) {
            $this->error('Gagal mengambil harga emas.');
            return self::FAILURE;
        }

        $admin = User::where('role', 'admin')->first();

        GoldPrice::updateOrCreate(
            ['price_date' => today()->toDateString()],
            [
                'buy_price_per_gram'  => $data['buy'],
                'sell_price_per_gram' => $data['sell'],
                'source'              => $data['source'],
                'recorded_by'         => $admin?->id ?? 1,
                'notes'               => 'Auto-fetch via scheduled command.',
            ]
        );

        $this->service->clearCache();

        $this->info("✅ Harga disimpan: Beli Rp " . number_format($data['buy'],0,',','.') . " | Jual Rp " . number_format($data['sell'],0,',','.') . " (Sumber: {$data['source']})");

        return self::SUCCESS;
    }
}
