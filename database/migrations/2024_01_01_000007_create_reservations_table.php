<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reservations dibuat TANPA transaction_id dulu untuk menghindari
     * circular dependency dengan tabel transactions.
     * FK transaction_id ditambahkan di migration 2024_01_01_000009.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_code', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->smallInteger('quantity')->default(1);
            $table->date('preferred_date');
            $table->time('preferred_time')->nullable();
            $table->enum('status', [
                'pending',
                'confirmed',
                'ready',
                'completed',
                'cancelled',
                'expired',
            ])->default('pending');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            // transaction_id ditambahkan setelah tabel transactions dibuat (migration 000009)
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('preferred_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
