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
        Schema::table('recipe_items', function (Blueprint $table) {
            $table->dropForeign(['ingredient_id']);
            $table->dropForeign(['sub_product_id']);
            $table->dropColumn(['ingredient_id', 'sub_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_items', function (Blueprint $table) {
            $table->unsignedBigInteger('ingredient_id')->nullable()->after('inventory_item_id');
            $table->unsignedBigInteger('sub_product_id')->nullable()->after('ingredient_id');
            
            $table->foreign('ingredient_id')->references('id')->on('ingredients')->onDelete('cascade');
            $table->foreign('sub_product_id')->references('id')->on('product_items')->onDelete('cascade');
        });
    }
};
