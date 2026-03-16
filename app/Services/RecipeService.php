<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\ProductItem;
use App\Models\StockMaster;
use App\Models\StockLedger;
use Illuminate\Support\Facades\DB;
use Exception;

class RecipeService
{
    /**
     * Store or update a recipe for a menu item.
     */
    public function storeOrUpdate(array $data): Recipe
    {
        return DB::transaction(function () use ($data) {
            $recipe = Recipe::updateOrCreate(
                ['menu_item_id' => $data['menu_item_id']],
                ['branch_id' => $data['branch_id'] ?? null]
            );

            // Sync recipe items
            $recipe->recipeItems()->delete();

            $itemsData = array_map(function ($item) use ($recipe) {
                return [
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $item['ingredient_id'],
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $data['items']);

            RecipeItem::insert($itemsData);

            return $recipe->load('recipeItems.ingredient.unit');
        });
    }

    /**
     * Deduct stock for ingredients in a menu item's recipe.
     */
    public function deductStockForProduct(int $productItemId, float $quantity, string $referenceType, int $referenceId): void
    {
        $recipe = Recipe::with('recipeItems')->where('menu_item_id', $productItemId)->first();

        if (!$recipe) {
            return;
        }

        foreach ($recipe->recipeItems as $recipeItem) {
            $totalDeduct = $recipeItem->quantity * $quantity;

            DB::transaction(function () use ($recipeItem, $totalDeduct, $referenceType, $referenceId) {
                // Update Stock Master
                $stockMaster = StockMaster::where('ingredient_id', $recipeItem->ingredient_id)->first();
                
                if ($stockMaster) {
                    $stockMaster->current_stock -= $totalDeduct;
                    $stockMaster->total_out += $totalDeduct;
                    $stockMaster->save();
                } else {
                    // This case should ideally not happen if stock is managed properly
                    StockMaster::create([
                        'ingredient_id' => $recipeItem->ingredient_id,
                        'current_stock' => -$totalDeduct,
                        'total_in' => 0,
                        'total_out' => $totalDeduct
                    ]);
                }

                // Insert to Stock Ledger
                StockLedger::create([
                    'ingredient_id' => $recipeItem->ingredient_id,
                    'transaction_type' => 'out',
                    'quantity' => $totalDeduct,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'notes' => 'Stock deduction for recipe item in ' . $referenceType . ' #' . $referenceId,
                    'transaction_date' => now()
                ]);
            });
        }
    }
}
