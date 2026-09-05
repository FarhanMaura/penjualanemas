<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installment_plans', function (Blueprint $table) {
            $table->foreignId('pickup_reservation_id')->nullable()->after('notes')->constrained('reservations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('installment_plans', function (Blueprint $table) {
            $table->dropForeign(['pickup_reservation_id']);
            $table->dropColumn('pickup_reservation_id');
        });
    }
};
