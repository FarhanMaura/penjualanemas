<?php

namespace Database\Seeders;

use App\Models\RewardProgram;
use Illuminate\Database\Seeder;

class RewardProgramSeeder extends Seeder
{
    public function run(): void
    {
        RewardProgram::updateOrCreate(
            ['name' => 'Poin per Transaksi'],
            [
                'description'             => 'Dapatkan 1 poin setiap kali menyelesaikan transaksi di toko.',
                'type'                    => 'points',
                'points_per_transaction'  => 1,
                'min_transaction_amount'  => 100000,
                'is_active'               => true,
                'earn_rule'               => ['event' => 'transaction_completed', 'points' => 1],
                'redeem_rule'             => null,
            ]
        );

        RewardProgram::updateOrCreate(
            ['name' => 'Reward ke-10 Transaksi – Gold'],
            [
                'description'             => 'Pelanggan yang mencapai 10 transaksi selesai mendapatkan diskon khusus dan cuci emas gratis.',
                'type'                    => 'gift',
                'points_per_transaction'  => 0,
                'min_transaction_amount'  => 0,
                'is_active'               => true,
                'earn_rule'               => ['milestone_transactions' => 10],
                'redeem_rule'             => ['benefit' => 'diskon_khusus + cuci_emas_gratis'],
            ]
        );
    }
}
