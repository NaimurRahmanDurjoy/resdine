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
            if (!Schema::hasColumn('order_masters', 'service_charge_amount')) {
                $table->decimal('service_charge_amount', 10, 2)->default(0.00)->after('discount');
            }
        });

        Schema::table('invoice_master', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_master', 'service_charge_amount')) {
                $table->decimal('service_charge_amount', 10, 2)->default(0.00)->after('tax_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_masters', function (Blueprint $table) {
            if (Schema::hasColumn('order_masters', 'service_charge_amount')) {
                $table->dropColumn('service_charge_amount');
            }
        });

        Schema::table('invoice_master', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_master', 'service_charge_amount')) {
                $table->dropColumn('service_charge_amount');
            }
        });
    }
};
