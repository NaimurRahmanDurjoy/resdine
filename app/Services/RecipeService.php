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
                [
                    'menu_item_id' => $data['menu_item_id'],
                    'variant_id' => $data['variant_id'] ?? null
                ],
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
    public function deductStockForProduct(int $productItemId, ?int $variantId, float $quantity, string $referenceType, int $referenceId, int $branchId): void
    {
        // Find recipe: specific variant recipe first, then general item recipe
        $recipe = Recipe::with('recipeItems')
            ->where('menu_item_id', $productItemId)
            ->where('variant_id', $variantId)
            ->first();

        if (!$recipe && $variantId) {
             $recipe = Recipe::with('recipeItems')
                ->where('menu_item_id', $productItemId)
                ->whereNull('variant_id')
                ->first();
        }

        if (!$recipe) {
            return;
        }

        foreach ($recipe->recipeItems as $recipeItem) {
            $totalDeduct = $recipeItem->quantity * $quantity;

            DB::transaction(function () use ($recipeItem, $totalDeduct, $referenceType, $referenceId, $branchId) {
                // Update Stock Summary
                $stockSummary = \App\Models\StockSummary::where('ingredient_id', $recipeItem->ingredient_id)
                    ->where('branch_id', $branchId)
                    ->first();
                
                if ($stockSummary) {
                    $stockSummary->current_stock -= $totalDeduct;
                    $stockSummary->last_transaction_date = now();
                    $stockSummary->save();
                } else {
                    \App\Models\StockSummary::create([
                        'ingredient_id' => $recipeItem->ingredient_id,
                        'unit_id' => $recipeItem->unit_id,
                        'branch_id' => $branchId,
                        'current_stock' => -$totalDeduct,
                        'last_transaction_date' => now()
                    ]);
                }

                // Insert to Stock Ledger
                \App\Models\StockLedger::create([
                    'ingredient_id' => $recipeItem->ingredient_id,
                    'unit_id' => $recipeItem->unit_id,
                    'branch_id' => $branchId,
                    'transaction_type' => 2, // 2=sale
                    'qty_out' => $totalDeduct,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'transaction_date' => now()
                ]);
            });
        }
    }
}
