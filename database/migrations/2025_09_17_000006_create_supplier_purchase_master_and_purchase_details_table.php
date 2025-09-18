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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('contact')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('purchase_master', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('user_id')->nullable(); // who created
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->date('purchase_date');
            $table->tinyInteger('status')->default(1)->comment('0=pending,1=approved,2=received,3=cancelled');
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('purchase_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('ingredients_id');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total', 10, 2);
            $table->timestamps();

            $table->foreign('purchase_id')->references('id')->on('purchase_master')->onDelete('cascade');
            $table->foreign('ingredients_id')->references('id')->on('ingredients')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('purchase_master');
        Schema::dropIfExists('purchase_details');
    }
};
