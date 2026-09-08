<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $admins = User::where('role', 'admin')->get();
        $customers = User::where('role', 'customer')->get();

        foreach ($customers as $customer) {
            Notification::firstOrCreate(
                [
                    'user_id' => $customer->id,
                    'title'   => 'Selamat Datang di Toko Emas Sinar Baru II',
                ],
                [
                    'type'       => 'system.welcome',
                    'message'    => 'Sistem penjualan, cicilan, dan gadai emas murni 24K kini aktif. Selamat bertransaksi!',
                    'data'       => [],
                    'read_at'    => null,
                    'created_at' => now()->subHours(5),
                ]
            );

            Notification::firstOrCreate(
                [
                    'user_id' => $customer->id,
                    'title'   => 'Update Harga Emas Murni 24K',
                ],
                [
                    'type'       => 'price.info',
                    'message'    => 'Harga jual & buyback emas murni 24K hari ini telah diperbarui di sistem.',
                    'data'       => [],
                    'read_at'    => null,
                    'created_at' => now()->subHours(2),
                ]
            );
        }

        foreach ($admins as $admin) {
            Notification::firstOrCreate(
                [
                    'user_id' => $admin->id,
                    'title'   => 'Sistem Operasional Siap',
                ],
                [
                    'type'       => 'system.info',
                    'message'    => 'Panel admin siap mengelola reservasi, transaksi, cicilan, dan gadai emas.',
                    'data'       => [],
                    'read_at'    => null,
                    'created_at' => now()->subHours(4),
                ]
            );
        }
    }
}
