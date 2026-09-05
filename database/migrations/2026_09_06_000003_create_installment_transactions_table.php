<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installment_plan_id')->constrained('installment_plans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('transaction_code', 50)->unique();
            $table->decimal('amount', 12, 2);
            $table->json('months_paid'); // Array nomor angsuran, cth: [1, 2]
            $table->smallInteger('month_count')->default(1);
            $table->string('payment_method', 50)->default('transfer');
            $table->string('proof_image')->nullable();
            $table->string('sender_bank', 50)->nullable();
            $table->string('sender_name', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('waiting_verification'); // waiting_verification, verified, rejected
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['installment_plan_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        Schema::table('installment_payments', function (Blueprint $table) {
            $table->foreignId('installment_transaction_id')->nullable()->after('status')->constrained('installment_transactions')->nullOnDelete();
            $table->string('proof_image')->nullable()->after('installment_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('installment_payments', function (Blueprint $table) {
            $table->dropForeign(['installment_transaction_id']);
            $table->dropColumn(['installment_transaction_id', 'proof_image']);
        });

        Schema::dropIfExists('installment_transactions');
    }
};
