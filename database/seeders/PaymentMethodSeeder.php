<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name'           => 'Tunai di Toko (Cash)',
                'code'           => 'cash',
                'type'           => 'cash',
                'bank_name'      => null,
                'account_number' => null,
                'account_name'   => null,
                'instructions'   => 'Pembayaran tunai langsung di kasir Toko Emas Sinar Baru II saat serah terima barang.',
                'is_active'      => true,
                'sort_order'     => 1,
            ],
            [
                'name'           => 'Transfer Bank BCA',
                'code'           => 'bca',
                'type'           => 'bank_transfer',
                'bank_name'      => 'Bank Central Asia (BCA)',
                'account_number' => '8820 9182 34',
                'account_name'   => 'TOKO EMAS SINAR BARU II',
                'instructions'   => 'Transfer melalui ATM / KlikBCA / myBCA. Simpan bukti transfer untuk verifikasi toko.',
                'is_active'      => true,
                'sort_order'     => 2,
            ],
            [
                'name'           => 'Transfer Bank Mandiri',
                'code'           => 'mandiri',
                'type'           => 'bank_transfer',
                'bank_name'      => 'Bank Mandiri',
                'account_number' => '113 00 1829 4432',
                'account_name'   => 'TOKO EMAS SINAR BARU II',
                'instructions'   => 'Transfer via Livin by Mandiri atau ATM. Harap cantumkan kode transaksi di kolom berita acara.',
                'is_active'      => true,
                'sort_order'     => 3,
            ],
            [
                'name'           => 'Transfer Bank BRI',
                'code'           => 'bri',
                'type'           => 'bank_transfer',
                'bank_name'      => 'Bank Rakyat Indonesia (BRI)',
                'account_number' => '0089 01 028472 50 1',
                'account_name'   => 'TOKO EMAS SINAR BARU II',
                'instructions'   => 'Transfer via BRImo atau ATM BRI. Konfirmasi pembayaran ke kasir toko.',
                'is_active'      => true,
                'sort_order'     => 4,
            ],
            [
                'name'           => 'QRIS (Semua E-Wallet / Mobile Banking)',
                'code'           => 'qris',
                'type'           => 'qris',
                'bank_name'      => 'QRIS Standar Indonesia',
                'account_number' => 'NMID: ID1020039281728',
                'account_name'   => 'TOKO EMAS SINAR BARU II',
                'instructions'   => 'Scan QRIS menggunakan GoPay, OVO, Dana, ShopeePay, atau aplikasi mobile banking apa pun di kasir toko.',
                'is_active'      => true,
                'sort_order'     => 5,
            ],
            [
                'name'           => 'Kartu Debit (EDC Toko)',
                'code'           => 'debit',
                'type'           => 'debit',
                'bank_name'      => 'Mesin EDC Toko',
                'account_number' => null,
                'account_name'   => null,
                'instructions'   => 'Gesek kartu debit BCA, Mandiri, BRI, BNI langsung di mesin EDC kasir toko.',
                'is_active'      => true,
                'sort_order'     => 6,
            ],
            [
                'name'           => 'Kartu Kredit (Visa / Mastercard)',
                'code'           => 'credit',
                'type'           => 'credit',
                'bank_name'      => 'Mesin EDC Toko',
                'account_number' => null,
                'account_name'   => null,
                'instructions'   => 'Pembayaran menggunakan kartu kredit Visa / Mastercard di kasir toko.',
                'is_active'      => true,
                'sort_order'     => 7,
            ],
        ];

        foreach ($methods as $m) {
            PaymentMethod::updateOrCreate(['code' => $m['code']], $m);
        }
    }
}
