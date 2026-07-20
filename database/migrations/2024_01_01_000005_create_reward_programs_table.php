<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->enum('type', ['points', 'cashback', 'discount', 'gift', 'tier_upgrade']);
            $table->json('earn_rule')->nullable()->comment('Aturan perolehan poin, misal: {"per_amount":100000,"points":1}');
            $table->json('redeem_rule')->nullable()->comment('Aturan penukaran, misal: {"points":100,"discount":10000}');
            $table->integer('points_per_transaction')->default(0);
            $table->decimal('min_transaction_amount', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_programs');
    }
};
