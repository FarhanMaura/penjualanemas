<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('price_negotiation_id')->nullable()->after('product_id')->constrained('price_negotiations')->nullOnDelete();
            $table->decimal('agreed_price', 12, 2)->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['price_negotiation_id']);
            $table->dropColumn(['price_negotiation_id', 'agreed_price']);
        });
    }
};
