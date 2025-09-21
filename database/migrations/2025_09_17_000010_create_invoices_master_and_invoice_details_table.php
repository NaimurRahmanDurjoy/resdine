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
        Schema::create('invoice_master', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('promotion_id')->nullable();
            $table->decimal('sub_total', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('collect_amount', 10, 2)->default(0);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->tinyInteger('status')->default(1)->comment('1=pending,2=paid,3=cancelled');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('order_master')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('set null');
        });

        Schema::create('invoice_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('item_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoice_master')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('menu_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices_master');
        Schema::dropIfExists('invoice_details');
    }
};
