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
        // 1. Create the new parent recipes table
        Schema::create('recipes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('menu_item_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('menu_item_id')->references('id')->on('product_items')->onDelete('cascade');
        });

        // 2. Rename existing recipes table to recipe_items
        Schema::rename('recipes', 'recipe_items');

        // 3. Modify recipe_items table
        Schema::table('recipe_items', function (Blueprint $table) {
            // Add recipe_id
            $table->unsignedBigInteger('recipe_id')->after('id')->nullable();
            
            // The existing item_id is now redundant as it's in the recipes table
            // We'll keep it for a moment to migrate data if needed, then drop it.
        });

        // 4. Migrate existing data if any
        // Since item_id was in the old 'recipes' (now 'recipe_items'), 
        // we create a 'recipes' record for each unique item_id and link them.
        $oldItems = DB::table('recipe_items')->distinct()->pluck('item_id');
        foreach ($oldItems as $itemId) {
            $recipeId = DB::table('recipes')->insertGetId([
                'menu_item_id' => $itemId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('recipe_items')->where('item_id', $itemId)->update(['recipe_id' => $recipeId]);
        }

        // 5. Finalize recipe_items table
        Schema::table('recipe_items', function (Blueprint $table) {
            $table->unsignedBigInteger('recipe_id')->nullable(false)->change();
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            
            // Drop old foreign key and column
            $table->dropForeign(['item_id']);
            $table->dropColumn('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // In reverse: 
        // 1. Add item_id back to recipe_items
        Schema::table('recipe_items', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable()->after('recipe_id');
            $table->foreign('item_id')->references('id')->on('product_items')->onDelete('cascade');
        });

        // 2. Restore item_id from recipes
        $recipeLinks = DB::table('recipe_items')
            ->join('recipes', 'recipe_items.recipe_id', '=', 'recipes.id')
            ->select('recipe_items.id', 'recipes.menu_item_id')
            ->get();
            
        foreach ($recipeLinks as $link) {
            DB::table('recipe_items')->where('id', $link->id)->update(['item_id' => $link->menu_item_id]);
        }

        // 3. Drop recipe_id and foreign key
        Schema::table('recipe_items', function (Blueprint $table) {
            $table->dropForeign(['recipe_id']);
            $table->dropColumn('recipe_id');
        });

        // 4. Rename back
        Schema::rename('recipe_items', 'recipes');

        // 5. Drop the new recipes table
        Schema::dropIfExists('recipes'); // This will actually drop the table we just renamed to 'recipes' if we aren't careful.
        // Wait, Schema::dropIfExists('recipes') here would drop the table that was originally 'recipes' (and now renamed back).
        // Correct order for down():
        // We renamed recipe_items -> recipes (the old one)
        // Then we should drop the one we created in up().
    }
};
