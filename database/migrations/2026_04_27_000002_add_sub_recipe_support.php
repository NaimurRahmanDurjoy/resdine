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
        Schema::table('product_items', function (Blueprint $table) {
            $table->boolean('is_prep_item')->default(false)->after('type');
        });

        Schema::table('recipe_items', function (Blueprint $table) {
            $table->unsignedBigInteger('ingredient_id')->nullable()->change();
            $table->unsignedBigInteger('sub_product_id')->nullable()->after('ingredient_id');
            
            $table->foreign('sub_product_id')->references('id')->on('product_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_items', function (Blueprint $table) {
            $table->dropForeign(['sub_product_id']);
            $table->dropColumn('sub_product_id');
            $table->unsignedBigInteger('ingredient_id')->nullable(false)->change();
        });

        Schema::table('product_items', function (Blueprint $table) {
            $table->dropColumn('is_prep_item');
        });
    }
};
