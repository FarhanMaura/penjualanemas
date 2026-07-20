<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan penting: user → kategori → produk → harga emas → reward
        $this->call([
            AdminUserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            GoldPriceSeeder::class,
            RewardProgramSeeder::class,
            TransactionAndCertificateSeeder::class,
        ]);
    }
}
