<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Ubah product_id menjadi nullable
            $table->foreignId('product_id')->nullable()->change();

            // Tambahkan kolom type
            $table->string('type', 30)->default('purchase')->after('user_id');

            // Detail Gadai yang diajukan
            $table->text('pawn_gold_description')->nullable()->after('notes');
            $table->string('pawn_gold_purity', 20)->nullable()->after('pawn_gold_description');
            $table->decimal('pawn_weight_gram', 12, 3)->nullable()->after('pawn_gold_purity');
            $table->decimal('pawn_amount_requested', 15, 2)->nullable()->after('pawn_weight_gram');

            // Detail Cicilan yang diajukan
            $table->integer('installment_tenure')->nullable()->after('pawn_amount_requested');
            $table->decimal('installment_down_payment', 15, 2)->nullable()->after('installment_tenure');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable(false)->change();
            $table->dropColumn([
                'type',
                'pawn_gold_description',
                'pawn_gold_purity',
                'pawn_weight_gram',
                'pawn_amount_requested',
                'installment_tenure',
                'installment_down_payment',
            ]);
        });
    }
};
