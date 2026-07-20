<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pawns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->unique()->constrained('transactions')->cascadeOnDelete();
            $table->string('pawn_code', 30)->unique();
            $table->text('gold_description')->comment('Deskripsi emas yang digadai');
            $table->string('gold_purity', 10);
            $table->decimal('weight_gram', 8, 3);
            $table->decimal('appraised_value', 15, 2)->comment('Nilai taksiran');
            $table->decimal('loan_amount', 15, 2)->comment('Pinjaman yang diberikan');
            $table->decimal('interest_rate', 5, 2)->comment('Bunga per bulan (%)');
            $table->date('start_date');
            $table->date('due_date');
            $table->enum('status', ['active', 'redeemed', 'extended', 'forfeited'])->default('active');
            $table->date('redemption_date')->nullable();
            $table->decimal('redemption_amount', 15, 2)->nullable()->comment('Total dibayar saat tebus');
            // Referensi asal barang — untuk buyback & gadai, wajib dari riwayat pembelian pelanggan
            $table->foreignId('transaction_item_id')
                ->nullable()
                ->constrained('transaction_items')
                ->nullOnDelete()
                ->comment('Referensi item pembelian asal; nullable untuk emas non-katalog');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('pawn_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pawns');
    }
};
