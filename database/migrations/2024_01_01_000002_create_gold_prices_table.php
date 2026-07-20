<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gold_prices', function (Blueprint $table) {
            $table->id();
            $table->date('price_date')->unique();
            $table->decimal('buy_price_per_gram', 12, 2)->comment('Harga beli emas dari pelanggan');
            $table->decimal('sell_price_per_gram', 12, 2)->comment('Harga jual emas ke pelanggan');
            $table->string('source', 100)->nullable()->comment('Sumber harga: ANTAM, lokal, dll.');
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gold_prices');
    }
};
