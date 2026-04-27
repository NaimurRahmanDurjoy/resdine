<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\ProductItem;
use App\Models\StockSummary;
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
                    'ingredient_id' => $item['ingredient_id'] ?: null,
                    'sub_product_id' => $item['sub_product_id'] ?: null,
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'] ?: null,
                    'wastage_percentage' => $item['wastage_percentage'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $data['items']);

            RecipeItem::insert($itemsData);

            return $recipe->load(['recipeItems.ingredient.unit', 'recipeItems.subProduct.recipe']);
        });
    }

    /**
     * Get the latest purchase cost for an ingredient, normalized to a target unit.
     */
    public function getLatestIngredientCost(int $ingredientId, ?int $targetUnitId = null): float
    {
        $ingredient = \App\Models\Ingredient::find($ingredientId);
        if (!$ingredient) return 0;

        // 1. Try to get Average Cost from Stock Summary (most accurate for current stock)
        $stockSummary = \App\Models\StockSummary::where('ingredient_id', $ingredientId)->first();
        $cost = 0;
        
        if ($stockSummary && $stockSummary->average_cost > 0) {
            $cost = (float) $stockSummary->average_cost;
        } else {
            // 2. Fallback to latest purchase cost
            $latestPurchase = \App\Models\PurchaseDetail::where('ingredients_id', $ingredientId)
                ->whereHas('purchase', function ($q) {
                    $q->where('status', 2); // 2 = Received/Approved in PurchaseController
                })
                ->latest()
                ->first();

            if ($latestPurchase) {
                $cost = (float) $latestPurchase->unit_price;
            } else {
                // 3. Fallback to manual cost set on ingredient
                $cost = (float) ($ingredient->cost ?? 0);
            }
        }

        if ($cost <= 0) return 0;

        $baseUnitId = $ingredient->unit_id;

        if ($targetUnitId && $baseUnitId != $targetUnitId) {
            $conversionFactor = $this->convertQuantity(1, $targetUnitId, $baseUnitId);
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
            $unitCost = 0;
            $name = 'Unknown';

            if ($item->ingredient_id) {
                $unitCost = $this->getLatestIngredientCost($item->ingredient_id, $item->unit_id);
                $name = $item->ingredient->name;
            } elseif ($item->sub_product_id) {
                $subProduct = $item->subProduct;
                $subRecipe = $subProduct->recipe;
                
                if ($subRecipe) {
                    $baseCost = $this->calculateRecipeCost($subRecipe)['total_cost'];
                    
                    // Normalize cost if the unit used in this recipe differs from sub-product's base unit
                    if ($item->unit_id && $subProduct->unit_id && $item->unit_id != $subProduct->unit_id) {
                        $conversionFactor = $this->convertQuantity(1, $item->unit_id, $subProduct->unit_id);
                        $unitCost = $baseCost * $conversionFactor;
                    } else {
                        $unitCost = $baseCost;
                    }
                }
                $name = $subProduct->name;
            }
            
            // Gross quantity = Net / (1 - Wastage%)
            $wastage = $item->wastage_percentage ?? 0;
            $grossQty = $wastage < 100 ? ($item->quantity / (1 - ($wastage / 100))) : $item->quantity;
            
            $itemCost = $unitCost * $grossQty;
            $totalCost += $itemCost;

            $items[] = [
                'ingredient_id' => $item->ingredient_id,
                'sub_product_id' => $item->sub_product_id,
                'name' => $name,
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
            $wastage = $recipeItem->wastage_percentage ?? 0;
            $grossQuantity = $wastage < 100 ? ($netQuantity / (1 - ($wastage / 100))) : $netQuantity;

            if ($recipeItem->ingredient_id) {
                // Convert to ingredient's base unit if necessary
                $deductionQuantity = $this->convertQuantity($grossQuantity, $recipeItem->unit_id, $recipeItem->ingredient->unit_id);
                $this->updateStock($recipeItem->ingredient_id, $deductionQuantity, $branchId, 'out', $referenceType, $referenceId);
            } elseif ($recipeItem->sub_product_id) {
                // RECURSIVE CALL: Explode sub-recipe
                // We need to pass the quantity in the sub-product's base unit
                $subProduct = $recipeItem->subProduct;
                $normalizedQty = $grossQuantity;
                
                if ($recipeItem->unit_id && $subProduct->unit_id && $recipeItem->unit_id != $subProduct->unit_id) {
                    $normalizedQty = $this->convertQuantity($grossQuantity, $recipeItem->unit_id, $subProduct->unit_id);
                }

                $this->deductStockForProduct($recipeItem->sub_product_id, null, $normalizedQty, $referenceType, $referenceId, $branchId);
            }
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

            if ($recipeItem->ingredient_id) {
                $restorationQuantity = $this->convertQuantity($grossQuantity, $recipeItem->unit_id, $recipeItem->ingredient->unit_id);
                $this->updateStock($recipeItem->ingredient_id, $restorationQuantity, $branchId, 'in', $referenceType, $referenceId);
            } elseif ($recipeItem->sub_product_id) {
                $subProduct = $recipeItem->subProduct;
                $normalizedQty = $grossQuantity;
                
                if ($recipeItem->unit_id && $subProduct->unit_id && $recipeItem->unit_id != $subProduct->unit_id) {
                    $normalizedQty = $this->convertQuantity($grossQuantity, $recipeItem->unit_id, $subProduct->unit_id);
                }

                $this->restoreStockForProduct($recipeItem->sub_product_id, null, $normalizedQty, $referenceType, $referenceId, $branchId);
            }
        }
    }

    /**
     * Helper to get recipe (specific variant first, then fallback to general).
     */
    protected function getRecipe(int $productItemId, ?int $variantId): ?Recipe
    {
        $recipe = Recipe::with(['recipeItems.ingredient', 'recipeItems.subProduct'])
            ->where('menu_item_id', $productItemId)
            ->where('variant_id', $variantId)
            ->first();

        if (!$recipe && $variantId) {
             $recipe = Recipe::with(['recipeItems.ingredient', 'recipeItems.subProduct'])
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
                    $ingredient = \App\Models\Ingredient::find($ingredientId);
                    $stockSummary = \App\Models\StockSummary::create([
                        'ingredient_id' => $ingredientId,
                        'unit_id' => $ingredient->unit_id ?? null,
                        'branch_id' => $branchId,
                        'current_stock' => -$qty,
                        'last_transaction_date' => now()
                    ]);
                }
            } else {
                if ($stockSummary) {
                    $stockSummary->current_stock += $qty;
                } else {
                    $ingredient = \App\Models\Ingredient::find($ingredientId);
                    $stockSummary = \App\Models\StockSummary::create([
                        'ingredient_id' => $ingredientId,
                        'unit_id' => $ingredient->unit_id ?? null,
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
            throw new Exception("Invalid units provided for conversion.");
        }

        // 1. Get the Absolute Base Value of 'from' unit (recursively)
        $fromBaseData = $this->getAbsoluteBaseValue($fromUnit, $quantity);
        
        // 2. Get the Absolute Base Value of 'to' unit (recursively, for 1 unit)
        $toBaseData = $this->getAbsoluteBaseValue($toUnit, 1);

        // 3. Verify they share the same ultimate base unit root (e.g. Grams)
        if ($fromBaseData['root_unit_id'] !== $toBaseData['root_unit_id']) {
            throw new Exception("Incompatible units: Cannot convert {$fromUnit->name} (Root: {$fromBaseData['root_unit_name']}) to {$toUnit->name} (Root: {$toBaseData['root_unit_name']}).");
        }

        // 4. Calculate the converted quantity
        return (float) ($fromBaseData['value'] / $toBaseData['value']);
    }

    /**
     * Recursively calculate the value in the ultimate base unit.
     */
    protected function getAbsoluteBaseValue($unit, float $quantity): array
    {
        $currentValue = $quantity;
        $currentUnit = $unit;
        $visited = [$unit->id];

        while ($currentUnit->base_unit_id) {
            $currentValue *= (float) $currentUnit->conversion_factor;
            $nextUnit = \App\Models\Unit::find($currentUnit->base_unit_id);
            
            if (!$nextUnit || in_array($nextUnit->id, $visited)) {
                break; 
            }
            
            $currentUnit = $nextUnit;
            $visited[] = $currentUnit->id;
        }

        return [
            'value' => $currentValue,
            'root_unit_id' => $currentUnit->id,
            'root_unit_name' => $currentUnit->name
        ];
    }
}
