<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `installment_payments` MODIFY COLUMN `status` VARCHAR(30) NOT NULL DEFAULT 'pending'");
        } else {
            Schema::table('installment_payments', function (Blueprint $table) {
                $table->string('status', 30)->default('pending')->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `installment_payments` MODIFY COLUMN `status` ENUM('pending', 'paid', 'overdue', 'waived') NOT NULL DEFAULT 'pending'");
        } else {
            Schema::table('installment_payments', function (Blueprint $table) {
                $table->string('status', 30)->default('pending')->change();
            });
        }
    }
};
