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
        Schema::create('order_master', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('promotion_id')->nullable();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->unsignedBigInteger('table_id')->nullable()->comment('null if takeaway/delivery');
            $table->tinyInteger('order_type')->default(1)->comment('1=dine-in,2=takeaway,3=delivery,4=party/corporate');
            $table->tinyInteger('order_status')->default(0)->comment('0=pending,1=preparing,2=ready,3=out_for_delivery,4=completed,5=cancelled');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('collect_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2)->default(0)->comment('for partial payments');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('table_id')->references('id')->on('restaurant_tables')->onDelete('set null');
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('set null');
        });

        Schema::create('order_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('item_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('order_master')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('menu_items')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_master');
        Schema::dropIfExists('order_details');
    }
};
