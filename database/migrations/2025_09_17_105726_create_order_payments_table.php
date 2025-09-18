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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_master_id')->constrained('orders');
            $table->tinyInteger('method')->comment('1=cash,2=card,3=mobile banking,4=wallet');
            $table->decimal('cash_amount', 10, 2);
            $table->decimal('card_amount', 10, 2);
            $table->decimal('mfs_amount', 10, 2);
            $table->decimal('collect_amount', 10, 2);
            $table->decimal('due_amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
