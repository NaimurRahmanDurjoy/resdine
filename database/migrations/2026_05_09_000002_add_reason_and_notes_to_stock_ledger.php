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
        Schema::table('stock_ledger', function (Blueprint $table) {
            $table->string('reason', 50)->nullable()->after('qty_out')->comment('Reason for adjustment (e.g. damaged, expired, staff_meal)');
            $table->text('notes')->nullable()->after('reason')->comment('Additional context for the transaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_ledger', function (Blueprint $table) {
            $table->dropColumn(['reason', 'notes']);
        });
    }
};
