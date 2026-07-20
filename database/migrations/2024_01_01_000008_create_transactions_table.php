<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->enum('type', ['purchase', 'buyback', 'installment', 'pawn']);
            $table->enum('status', [
                'draft',
                'confirmed',
                'in_progress',
                'completed',
                'cancelled',
            ])->default('draft');
            $table->foreignId('gold_price_id')->nullable()->constrained('gold_prices')->nullOnDelete();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('admin_fee', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0)->comment('Diskon dari reward/poin');
            $table->decimal('total_amount', 15, 2);
            $table->string('payment_method', 50)->nullable()->comment('cash, transfer, dll. — dibayar di toko');
            $table->date('payment_date')->nullable();
            $table->string('payment_proof', 255)->nullable();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete();
            $table->foreignId('processed_by')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type', 'status']);
            $table->index('transaction_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
