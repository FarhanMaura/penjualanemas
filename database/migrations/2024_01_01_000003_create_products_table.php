<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('sku', 50)->unique();
            $table->string('name', 200);
            $table->string('slug', 220)->unique();
            $table->text('description')->nullable();
            $table->string('gold_purity', 10)->nullable()->comment('Kadar emas: 24K, 22K, 18K, dll.');
            $table->decimal('weight_gram', 8, 3)->comment('Berat dalam gram');
            $table->decimal('base_price', 15, 2)->comment('Harga dasar jual');
            $table->decimal('buy_back_price', 15, 2)->nullable()->comment('Harga beli kembali dari pelanggan');
            $table->integer('stock')->default(0);
            $table->json('images')->nullable()->comment('Array path gambar; index 0 = thumbnail utama');
            $table->boolean('is_available')->default(true);
            $table->boolean('is_reservable')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
