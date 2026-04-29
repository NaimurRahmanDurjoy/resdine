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

        // 3. SEED/MIGRATE DATA
        // This part would ideally be in a seeder or handled carefully in a production environment.
        // For this refactor, we migrate existing data.

        // Migrate Ingredients
        $ingredients = DB::table('ingredients')->get();
        foreach ($ingredients as $ing) {
            $itemId = DB::table('inventory_items')->insertGetId([
                'name' => $ing->name,
                'unit_id' => $ing->unit_id,
                'item_type' => 1,
                'reference_id' => $ing->id,
                'min_stock' => $ing->min_stock ?? 0,
                'cost' => $ing->cost ?? 0,
                'status' => $ing->status ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update associated records
            DB::table('stock_summary')->where('ingredient_id', $ing->id)->update(['inventory_item_id' => $itemId]);
            DB::table('stock_ledger')->where('ingredient_id', $ing->id)->update(['inventory_item_id' => $itemId]);
            DB::table('purchase_details')->where('ingredients_id', $ing->id)->update(['inventory_item_id' => $itemId]);
        }

        // Migrate Prep Items & Retail Products
        $products = DB::table('product_items')->get();
        foreach ($products as $prod) {
            // We only track stock for Prep Items or Retail items (you might want to adjust this logic)
            $itemId = DB::table('inventory_items')->insertGetId([
                'name' => $prod->name,
                'unit_id' => $prod->unit_id,
                'item_type' => $prod->is_prep_item ? 3 : 2,
                'reference_id' => $prod->id,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update associated records (from previous refactor attempts if columns existed)
            if (Schema::hasColumn('stock_summary', 'product_item_id')) {
                DB::table('stock_summary')->where('product_item_id', $prod->id)->update(['inventory_item_id' => $itemId]);
            }
            if (Schema::hasColumn('stock_ledger', 'product_item_id')) {
                DB::table('stock_ledger')->where('product_item_id', $prod->id)->update(['inventory_item_id' => $itemId]);
            }
            if (Schema::hasColumn('purchase_details', 'product_item_id')) {
                DB::table('purchase_details')->where('product_item_id', $prod->id)->update(['inventory_item_id' => $itemId]);
            }
        }

        // 4. Cleanup old columns
        Schema::table('stock_summary', function (Blueprint $table) {
            $table->dropForeign(['ingredient_id']);
            $table->dropColumn('ingredient_id');
            if (Schema::hasColumn('stock_summary', 'product_item_id')) {
                $table->dropForeign(['product_item_id']);
                $table->dropColumn('product_item_id');
            }
            
            // Re-enforce NOT NULL on new column
            $table->unsignedBigInteger('inventory_item_id')->nullable(false)->change();
            
            // Re-create unified index
            $table->unique(['inventory_item_id', 'branch_id', 'batch_no'], 'stock_summary_unique');
        });

        Schema::table('stock_ledger', function (Blueprint $table) {
            $table->dropColumn('ingredient_id');
            if (Schema::hasColumn('stock_ledger', 'product_item_id')) {
                $table->dropColumn('product_item_id');
            }
            $table->unsignedBigInteger('inventory_item_id')->nullable(false)->change();
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropForeign(['ingredients_id']);
            $table->dropColumn('ingredients_id');
            if (Schema::hasColumn('purchase_details', 'product_item_id')) {
                $table->dropForeign(['product_item_id']);
                $table->dropColumn('product_item_id');
            }
            $table->unsignedBigInteger('inventory_item_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversal logic would need to rebuild the old columns... 
        // Given the scale of this change, down() is primarily for schema cleanup.
        Schema::dropIfExists('inventory_items');
    }
};
