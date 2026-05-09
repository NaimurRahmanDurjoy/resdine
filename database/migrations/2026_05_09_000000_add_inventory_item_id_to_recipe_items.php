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
        Schema::table('recipe_items', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_item_id')->nullable()->after('recipe_id');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('cascade');
        });

        // Data migration
        $recipeItems = DB::table('recipe_items')->get();

        foreach ($recipeItems as $item) {
            $inventoryItemId = null;

            if ($item->ingredient_id) {
                // Find inventory item for ingredient (item_type = 1)
                $inventoryItemId = DB::table('inventory_items')
                    ->where('reference_id', $item->ingredient_id)
                    ->where('item_type', 1)
                    ->value('id');
            } elseif ($item->sub_product_id) {
                // Find inventory item for sub-product/prep item (item_type = 3)
                $inventoryItemId = DB::table('inventory_items')
                    ->where('reference_id', $item->sub_product_id)
                    ->whereIn('item_type', [2, 3])
                    ->value('id');
            }

            if ($inventoryItemId) {
                DB::table('recipe_items')
                    ->where('id', $item->id)
                    ->update(['inventory_item_id' => $inventoryItemId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropColumn('inventory_item_id');
        });
    }
};
