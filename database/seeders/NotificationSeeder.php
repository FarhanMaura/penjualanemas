<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $customer = User::where('role', 'customer')->first();

        if ($customer) {
            Notification::create([
                'user_id'    => $customer->id,
                'type'       => 'reward.earned',
                'title'      => 'Selamat! Anda Mendapatkan Poin Reward',
                'message'    => 'Transaksi Anda berhasil diverifikasi. Poin reward Anda bertambah +1 poin!',
                'data'       => ['points' => 1],
                'read_at'    => null,
                'created_at' => now()->subHours(2),
            ]);

            Notification::create([
                'user_id'    => $customer->id,
                'type'       => 'reservation.confirmed',
                'title'      => 'Reservasi Emas Dikonfirmasi',
                'message'    => 'Reservasi Anda telah dikonfirmasi oleh admin toko. Silakan datang ke toko sesuai jadwal.',
                'data'       => ['status' => 'confirmed'],
                'read_at'    => null,
                'created_at' => now()->subDay(),
            ]);

            Notification::create([
                'user_id'    => $customer->id,
                'type'       => 'price.info',
                'title'      => 'Update Harga Emas Hari Ini',
                'message'    => 'Harga buyback dan jual emas murni 24K telah diperbarui di sistem Toko Emas Sinar Baru II.',
                'data'       => [],
                'read_at'    => now()->subDays(2),
                'created_at' => now()->subDays(2),
            ]);
        }

        if ($admin) {
            Notification::create([
                'user_id'    => $admin->id,
                'type'       => 'reservation.created',
                'title'      => 'Reservasi Baru Masuk',
                'message'    => 'Pelanggan telah membuat reservasi baru untuk kunjungan ke toko. Mohon segera verifikasi.',
                'data'       => [],
                'read_at'    => null,
                'created_at' => now()->subHours(1),
            ]);

            Notification::create([
                'user_id'    => $admin->id,
                'type'       => 'negotiation.created',
                'title'      => 'Pengajuan Tawar Harga Baru',
                'message'    => 'Ada pengajuan penawaran harga produk emas yang menunggu respon persetujuan.',
                'data'       => [],
                'read_at'    => null,
                'created_at' => now()->subHours(3),
            ]);
        }
    }
}
