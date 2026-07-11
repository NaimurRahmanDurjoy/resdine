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
        Schema::table('order_masters', function (Blueprint $table) {
            $table->index(['order_status', 'created_at'], 'idx_orders_status_created');
        });

        Schema::table('stock_ledger', function (Blueprint $table) {
            $table->index(['inventory_item_id', 'branch_id'], 'idx_ledger_item_branch');
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->index('inventory_item_id', 'idx_purchase_details_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_masters', function (Blueprint $table) {
            $table->dropIndex('idx_orders_status_created');
        });

        Schema::table('stock_ledger', function (Blueprint $table) {
            $table->dropIndex('idx_ledger_item_branch');
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropIndex('idx_purchase_details_item');
        });
    }
};
