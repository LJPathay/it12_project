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
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->integer('balance_before')->nullable()->after('quantity');
            $table->integer('balance_after')->nullable()->after('balance_before');
        });

        Schema::table('inventory_batches', function (Blueprint $table) {
            $table->integer('previous_stock')->nullable()->after('remaining_quantity');
            $table->integer('total_stock_after')->nullable()->after('previous_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropColumn(['balance_before', 'balance_after']);
        });

        Schema::table('inventory_batches', function (Blueprint $table) {
            $table->dropColumn(['previous_stock', 'total_stock_after']);
        });
    }
};
