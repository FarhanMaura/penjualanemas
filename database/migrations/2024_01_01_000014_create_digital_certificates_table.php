<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number', 50)->unique();
            $table->foreignId('transaction_id')->unique()->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('issued_at');
            $table->string('pdf_path', 255)->nullable();
            $table->string('qr_code', 255)->nullable()->comment('Path atau data string QR Code verifikasi');
            $table->boolean('is_valid')->default(true);
            $table->timestamp('invalidated_at')->nullable();
            $table->text('invalidation_reason')->nullable();
            $table->timestamps();

            $table->index('certificate_number');
            $table->index(['user_id', 'is_valid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_certificates');
    }
};
