<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\GoldPrice;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\DigitalCertificate;
use Illuminate\Database\Seeder;

class TransactionAndCertificateSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('role', 'customer')->first();
        $admin = User::where('role', 'admin')->first();
        $product = Product::where('sku', 'JW-CIN-24K-01')->first(); // Cincin Solitaire
        $goldPrice = GoldPrice::latest('price_date')->first();

        if (!$customer || !$admin || !$product) {
            return;
        }

        // Create completed transaction
        $transaction = Transaction::create([
            'transaction_code' => 'TX-' . date('Ymd') . '-0001',
            'user_id'          => $customer->id,
            'type'             => 'purchase',
            'status'           => 'completed',
            'gold_price_id'    => $goldPrice?->id,
            'subtotal'         => $product->base_price,
            'admin_fee'        => 5000,
            'discount'         => 0,
            'total_amount'     => $product->base_price + 5000,
            'payment_method'   => 'transfer',
            'payment_date'     => today(),
            'processed_by'     => $admin->id,
            'notes'            => 'Pembelian lunas Cincin Solitaire Emas Putih',
        ]);

        // Create transaction item
        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id'     => $product->id,
            'product_name'   => $product->name,
            'gold_purity'    => $product->gold_purity,
            'weight_gram'    => $product->weight_gram,
            'quantity'       => 1,
            'price_per_unit' => $product->base_price,
            'subtotal'       => $product->base_price,
        ]);

        // Create digital certificate
        DigitalCertificate::create([
            'certificate_number' => 'CERT-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999)),
            'transaction_id'     => $transaction->id,
            'user_id'            => $customer->id,
            'issued_at'          => now(),
            'is_valid'           => true,
        ]);
    }
}
