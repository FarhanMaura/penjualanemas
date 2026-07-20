<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            // product_id nullable: untuk buyback emas bebas yang tidak terdaftar di katalog
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            // Snapshot data produk saat transaksi agar histori tidak berubah
            $table->string('product_name', 200)->comment('Snapshot nama produk saat transaksi');
            $table->string('gold_purity', 10)->nullable()->comment('Snapshot kadar emas');
            $table->decimal('weight_gram', 8, 3);
            $table->smallInteger('quantity')->default(1);
            $table->decimal('price_per_unit', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
