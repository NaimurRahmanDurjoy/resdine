<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create the central inventory_items table
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->nullable()->unique();
            $table->unsignedBigInteger('unit_id');
            $table->tinyInteger('item_type')->comment('1=Ingredient, 2=Product/Retail, 3=Prep Item');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('ID from original table');
            $table->decimal('min_stock', 10, 2)->default(0);
            $table->decimal('cost', 10, 2)->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });

        // 2. Add inventory_item_id to related tables
        Schema::table('stock_summary', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_item_id')->nullable()->after('id');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('cascade');
        });

        Schema::table('stock_ledger', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_item_id')->nullable()->after('id');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('cascade');
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_item_id')->nullable()->after('id');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('cascade');
        });

    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
