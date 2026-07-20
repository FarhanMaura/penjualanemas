<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->unique()->constrained('transactions')->cascadeOnDelete();
            $table->decimal('down_payment', 15, 2)->comment('Uang muka awal (dibayar di toko)');
            $table->decimal('total_installment', 15, 2)->comment('Total angsuran setelah DP');
            $table->smallInteger('tenure_months')->comment('Jumlah bulan cicilan');
            $table->decimal('monthly_amount', 12, 2)->comment('Nominal per bulan');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'completed', 'defaulted'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_plans');
    }
};
