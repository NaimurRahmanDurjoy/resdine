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
                    'wastage_percentage' => $item['wastage_percentage'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $data['items']);

            RecipeItem::insert($itemsData);

            return $recipe->load('recipeItems.ingredient.unit');
        });
    }

    /**
     * Get the latest purchase cost for an ingredient, normalized to a target unit.
     */
    public function getLatestIngredientCost(int $ingredientId, ?int $targetUnitId = null): float
    {
        $latestPurchase = \App\Models\PurchaseDetail::where('ingredients_id', $ingredientId)
            ->whereHas('purchase', function ($q) {
                $q->where('status', 1); // Only approved purchases
            })
            ->latest()
            ->first();

        if (!$latestPurchase) {
            return 0;
        }

        $cost = (float) $latestPurchase->unit_price;
        $purchaseUnitId = $latestPurchase->ingredient->unit_id; // Purchase price is usually for the ingredient's base/default unit

        if ($targetUnitId && $purchaseUnitId != $targetUnitId) {
            // If the purchase price is $10 for 1KG, and target is 1G:
            // We need to know how much 1G costs.
            // convertQuantity(1, target, purchase) gives how many KG is in 1G (e.g. 0.001)
            // Price per target unit = Price per purchase unit * (How many purchase units are in 1 target unit)
            $conversionFactor = $this->convertQuantity(1, $targetUnitId, $purchaseUnitId);
            return $cost * $conversionFactor;
        }

        return $cost;
    }

    /**
     * Calculate the total food cost of a recipe.
     */
    public function calculateRecipeCost(Recipe $recipe): array
    {
        $totalCost = 0;
        $items = [];

        foreach ($recipe->recipeItems as $item) {
            $unitCost = $this->getLatestIngredientCost($item->ingredient_id, $item->unit_id);
            
            // Gross quantity = Net / (1 - Wastage%)
            $wastage = $item->wastage_percentage ?? 0;
            $grossQty = $wastage < 100 ? ($item->quantity / (1 - ($wastage / 100))) : $item->quantity;
            
            $itemCost = $unitCost * $grossQty;
            $totalCost += $itemCost;

            $items[] = [
                'ingredient_id' => $item->ingredient_id,
                'ingredient_name' => $item->ingredient->name,
                'quantity' => $item->quantity,
                'wastage' => $wastage,
                'unit_cost' => $unitCost,
                'total_cost' => $itemCost
            ];
        }

        return [
            'total_cost' => round($totalCost, 2),
            'items' => $items
        ];
    }

    /**
     * Deduct stock for ingredients in a menu item's recipe.
     */
    public function deductStockForProduct(int $productItemId, ?int $variantId, float $quantity, string $referenceType, int $referenceId, int $branchId): void
    {
        $recipe = $this->getRecipe($productItemId, $variantId);

        if (!$recipe) {
            return;
        }

        foreach ($recipe->recipeItems as $recipeItem) {
            // Net quantity required for the order
            $netQuantity = $recipeItem->quantity * $quantity;
            
            // Total gross quantity to deduct (Net / (1 - Wastage%))
            // Example: 100g net with 10% wastage -> 100 / 0.9 = 111.11g
            $wastage = $recipeItem->wastage_percentage ?? 0;
            $grossQuantity = $wastage < 100 ? ($netQuantity / (1 - ($wastage / 100))) : $netQuantity;

            // Convert to ingredient's base unit if necessary
            $deductionQuantity = $this->convertQuantity($grossQuantity, $recipeItem->unit_id, $recipeItem->ingredient->unit_id);

            $this->updateStock($recipeItem->ingredient_id, $deductionQuantity, $branchId, 'out', $referenceType, $referenceId);
        }
    }

    /**
     * Restore stock for ingredients (e.g., when an order is cancelled).
     */
    public function restoreStockForProduct(int $productItemId, ?int $variantId, float $quantity, string $referenceType, int $referenceId, int $branchId): void
    {
        $recipe = $this->getRecipe($productItemId, $variantId);

        if (!$recipe) {
            return;
        }

        foreach ($recipe->recipeItems as $recipeItem) {
            $netQuantity = $recipeItem->quantity * $quantity;
            $wastage = $recipeItem->wastage_percentage ?? 0;
            $grossQuantity = $wastage < 100 ? ($netQuantity / (1 - ($wastage / 100))) : $netQuantity;

            $restorationQuantity = $this->convertQuantity($grossQuantity, $recipeItem->unit_id, $recipeItem->ingredient->unit_id);

            $this->updateStock($recipeItem->ingredient_id, $restorationQuantity, $branchId, 'in', $referenceType, $referenceId);
        }
    }

    /**
     * Helper to get recipe (specific variant first, then fallback to general).
     */
    protected function getRecipe(int $productItemId, ?int $variantId): ?Recipe
    {
        $recipe = Recipe::with('recipeItems.ingredient')
            ->where('menu_item_id', $productItemId)
            ->where('variant_id', $variantId)
            ->first();

        if (!$recipe && $variantId) {
             $recipe = Recipe::with('recipeItems.ingredient')
                ->where('menu_item_id', $productItemId)
                ->whereNull('variant_id')
                ->first();
        }

        return $recipe;
    }

    /**
     * Helper to update stock summary and ledger.
     */
    protected function updateStock(int $ingredientId, float $qty, int $branchId, string $direction, string $refType, int $refId): void
    {
        DB::transaction(function () use ($ingredientId, $qty, $branchId, $direction, $refType, $refId) {
            $stockSummary = \App\Models\StockSummary::where('ingredient_id', $ingredientId)
                ->where('branch_id', $branchId)
                ->first();

            if ($direction === 'out') {
                if ($stockSummary) {
                    $stockSummary->current_stock -= $qty;
                } else {
                    $stockSummary = \App\Models\StockSummary::create([
                        'ingredient_id' => $ingredientId,
                        'branch_id' => $branchId,
                        'current_stock' => -$qty,
                        'last_transaction_date' => now()
                    ]);
                }
            } else {
                if ($stockSummary) {
                    $stockSummary->current_stock += $qty;
                } else {
                    $stockSummary = \App\Models\StockSummary::create([
                        'ingredient_id' => $ingredientId,
                        'branch_id' => $branchId,
                        'current_stock' => $qty,
                        'last_transaction_date' => now()
                    ]);
                }
            }
            
            $stockSummary->last_transaction_date = now();
            $stockSummary->save();

            \App\Models\StockLedger::create([
                'ingredient_id' => $ingredientId,
                'unit_id' => $stockSummary->unit_id ?? null,
                'branch_id' => $branchId,
                'transaction_type' => $direction === 'out' ? 2 : 3, // 2=sale, 3=return/reversal
                'qty_in' => $direction === 'in' ? $qty : 0,
                'qty_out' => $direction === 'out' ? $qty : 0,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'transaction_date' => now()
            ]);
        });
    }

    /**
     * Convert quantity between units based on conversion factors.
     */
    public function convertQuantity(float $quantity, $fromUnitId, $toUnitId): float
    {
        if (!$fromUnitId || !$toUnitId || $fromUnitId == $toUnitId) {
            return $quantity;
        }

        $fromUnit = \App\Models\Unit::find($fromUnitId);
        $toUnit = \App\Models\Unit::find($toUnitId);

        if (!$fromUnit || !$toUnit) {
            return $quantity;
        }

        // Logic: Convert fromUnit to base unit, then convert base unit to toUnit
        // Case 1: fromUnit is a sub-unit, toUnit is its base unit
        if ($fromUnit->base_unit_id == $toUnit->id) {
            return $quantity * $fromUnit->conversion_factor;
        }

        // Case 2: toUnit is a sub-unit, fromUnit is its base unit
        if ($toUnit->base_unit_id == $fromUnit->id) {
            return $quantity / $toUnit->conversion_factor;
        }

        // Case 3: Both are sub-units of the same base unit
        if ($fromUnit->base_unit_id && $fromUnit->base_unit_id == $toUnit->base_unit_id) {
            $baseQty = $quantity * $fromUnit->conversion_factor;
            return $baseQty / $toUnit->conversion_factor;
        }

        return $quantity; // No conversion possible
    }
}
