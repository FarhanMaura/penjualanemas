<?php

namespace Database\Seeders;

use App\Models\GoldPrice;
use App\Models\User;
use Illuminate\Database\Seeder;

class GoldPriceSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (! $admin) return;

        // Harga dasar emas (per gram) hari ini
        $baseBuy  = 1_580_000;
        $baseSell = 1_620_000;

        // Buat data 30 hari terakhir dengan fluktuasi kecil
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();

            // Fluktuasi ±2%
            $fluctuation = (rand(-200, 200) / 10000);
            $buy  = round($baseBuy  * (1 + $fluctuation), -3);
            $sell = round($baseSell * (1 + $fluctuation), -3);

            GoldPrice::updateOrCreate(
                ['price_date' => $date],
                [
                    'buy_price_per_gram'  => $buy,
                    'sell_price_per_gram' => $sell,
                    'source'              => 'Manual (Seed Data)',
                    'recorded_by'         => $admin->id,
                    'notes'               => 'Data seeder untuk development.',
                ]
            );
        }
    }
}
