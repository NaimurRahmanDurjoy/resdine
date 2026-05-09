<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\ProductItem;
use App\Models\StockSummary;
use App\Models\StockLedger;
use App\Models\Ingredient;
use App\Models\PurchaseDetail;
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
                    'inventory_item_id' => $item['inventory_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'] ?? null,
                    'wastage_percentage' => $item['wastage_percentage'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $data['items']);

            RecipeItem::insert($itemsData);

            return $recipe->load(['recipeItems.inventoryItem.unit', 'recipeItems.inventoryItem.productItem.recipe']);
        });
    }

    /**
     * Get the latest purchase cost for an inventory item, normalized to a target unit.
     */
    public function getLatestItemCost(int $inventoryItemId, ?int $targetUnitId = null): float
    {
        $item = \App\Models\InventoryItem::find($inventoryItemId);
        if (!$item) return 0;

        // 1. Try to get Average Cost from Stock Summary
        $stockSummary = StockSummary::where('inventory_item_id', $inventoryItemId)->first();
        $cost = 0;
        
        if ($stockSummary && $stockSummary->average_cost > 0) {
            $cost = (float) $stockSummary->average_cost;
        } else {
            // 2. Fallback to latest purchase cost
            $latestPurchase = PurchaseDetail::where('inventory_item_id', $inventoryItemId)
                ->whereHas('purchase', function ($q) {
                    $q->where('status', 2);
                })
                ->latest()
                ->first();

            if ($latestPurchase) {
                $cost = (float) ($latestPurchase->total_price / $latestPurchase->normalized_quantity);
            } else {
                // 3. Fallback to manual cost set on inventory item or source
                $cost = (float) ($item->cost ?? 0);
            }
        }

        if ($cost <= 0) return 0;

        $baseUnitId = $item->unit_id;

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
            $invItem = $item->inventoryItem;

            if ($invItem) {
                $name = $invItem->name;
                
                if ($invItem->item_type == 1) { // Raw Ingredient
                    $unitCost = $this->getLatestItemCost($invItem->id, $item->unit_id);
                } elseif ($invItem->item_type == 3) { // Prep Item (Sub-Product)
                    $subProduct = $invItem->productItem;
                    $subRecipe = $subProduct ? $subProduct->recipe : null;
                    
                    if ($subRecipe) {
                        $baseCost = $this->calculateRecipeCost($subRecipe)['total_cost'];
                        
                        // Normalize cost if units differ
                        if ($item->unit_id && $subProduct->unit_id && $item->unit_id != $subProduct->unit_id) {
                            $conversionFactor = $this->convertQuantity(1, $item->unit_id, $subProduct->unit_id);
                            $unitCost = $baseCost * $conversionFactor;
                        } else {
                            $unitCost = $baseCost;
                        }
                    }
                } else {
                    // Fallback for other types if they have no recipe
                    $unitCost = $this->getLatestItemCost($invItem->id, $item->unit_id);
                }
            }
            
            // Gross quantity = Net / (1 - Wastage%)
            $wastage = $item->wastage_percentage ?? 0;
            $grossQty = $wastage < 100 ? ($item->quantity / (1 - ($wastage / 100))) : $item->quantity;
            
            $itemCost = $unitCost * $grossQty;
            $totalCost += $itemCost;

            $items[] = [
                'inventory_item_id' => $item->inventory_item_id,
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
     * Helper to update stock summary and ledger using the Unified Inventory Item ID.
     */
    protected function updateStockByInventoryItem(int $inventoryItemId, float $qty, int $branchId, string $direction, string $refType, int $refId): void
    {
        DB::transaction(function () use ($inventoryItemId, $qty, $branchId, $direction, $refType, $refId) {
            $inventoryItem = \App\Models\InventoryItem::findOrFail($inventoryItemId);

            $stockSummary = StockSummary::where('inventory_item_id', $inventoryItem->id)
                ->where('branch_id', $branchId)
                ->first();

            if ($direction === 'out') {
                if ($stockSummary) {
                    $stockSummary->current_stock -= $qty;
                } else {
                    $stockSummary = StockSummary::create([
                        'inventory_item_id' => $inventoryItem->id,
                        'unit_id' => $inventoryItem->unit_id,
                        'branch_id' => $branchId,
                        'current_stock' => -$qty,
                        'last_transaction_date' => now()
                    ]);
                }
            } else {
                if ($stockSummary) {
                    $stockSummary->current_stock += $qty;
                } else {
                    $stockSummary = StockSummary::create([
                        'inventory_item_id' => $inventoryItem->id,
                        'unit_id' => $inventoryItem->unit_id,
                        'branch_id' => $branchId,
                        'current_stock' => $qty,
                        'last_transaction_date' => now()
                    ]);
                }
            }
            
            $stockSummary->last_transaction_date = now();
            $stockSummary->save();

            StockLedger::create([
                'inventory_item_id' => $inventoryItem->id,
                'unit_id' => $stockSummary->unit_id,
                'branch_id' => $branchId,
                'transaction_type' => $direction === 'out' ? 2 : 3,
                'qty_in' => $direction === 'in' ? $qty : 0,
                'qty_out' => $direction === 'out' ? $qty : 0,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'transaction_date' => now()
            ]);
        });
    }

    /**
     * @deprecated Use updateStockByInventoryItem
     */
    protected function updateStock($item, float $qty, int $branchId, string $direction, string $refType, int $refId): void
    {
        $this->updateStockByInventoryItem($item->inventoryItem->id, $qty, $branchId, $direction, $refType, $refId);
    }

    /**
     * Validate stock availability for a recipe. Returns array of errors if insufficient.
     */
    public function validateStockForRecipe(int $productItemId, ?int $variantId, float $quantity, int $branchId): array
    {
        $product = ProductItem::with('inventoryItem')->find($productItemId);
        if (!$product) return ["Product not found."];

        // 1. Check for direct stock of the product first (Retail/Prep items)
        if ($product->inventoryItem) {
            $invItem = $product->inventoryItem;
            $inventoryQuantity = $this->convertQuantity($quantity, $product->unit_id, $invItem->unit_id);
            
            $stockSummary = StockSummary::where('inventory_item_id', $invItem->id)
                ->where('branch_id', $branchId)
                ->first();

            $currentStock = $stockSummary ? (float)$stockSummary->current_stock : 0;
            if ($currentStock >= $inventoryQuantity) {
                return []; // Direct stock is sufficient
            }
        }

        // 2. Fallback to recipe validation (Ingredients)
        $recipe = $this->getRecipe($productItemId, $variantId);
        if (!$recipe) {
            return ["No stock or recipe found for {$product->name}."];
        }

        $errors = [];
        foreach ($recipe->recipeItems as $recipeItem) {
            $netQuantity = $recipeItem->quantity * $quantity;
            $wastage = $recipeItem->wastage_percentage ?? 0;
            $grossQuantity = $wastage < 100 ? ($netQuantity / (1 - ($wastage / 100))) : $netQuantity;

            $invItem = $recipeItem->inventoryItem;
            if (!$invItem) continue;

            if ($invItem->item_type == 1) { // Ingredient
                $requiredQty = $this->convertQuantity($grossQuantity, $recipeItem->unit_id, $invItem->unit_id);
                $stockSummary = StockSummary::where('inventory_item_id', $invItem->id)
                    ->where('branch_id', $branchId)
                    ->first();

                $currentStock = $stockSummary ? (float)$stockSummary->current_stock : 0;
                if ($currentStock < $requiredQty) {
                    $shortage = round($requiredQty - $currentStock, 3);
                    $errors[] = "{$invItem->name} (Missing {$shortage} " . ($invItem->unit->short_name ?? $invItem->unit->name) . ")";
                }
            } elseif ($invItem->item_type == 3) { // Prep Item (Sub-Product)
                $subErrors = $this->validateStockForRecipe($invItem->reference_id, null, $grossQuantity, $branchId);
                $errors = array_merge($errors, $subErrors);
            }
        }
        return $errors;
    }

    /**
     * Deduct stock for a product, using Unified Inventory IDs.
     */
    public function deductStockForProduct(int $productItemId, ?int $variantId, float $quantity, string $referenceType, int $referenceId, int $branchId): void
    {
        $product = ProductItem::with('inventoryItem')->find($productItemId);
        if (!$product) return;

        // Check for direct stock of the product first (Unified)
        if ($product->inventoryItem) {
            $invItem = $product->inventoryItem;
            // Convert selling quantity to inventory unit quantity
            $inventoryQuantity = $this->convertQuantity($quantity, $product->unit_id, $invItem->unit_id);

            $stockSummary = StockSummary::where('inventory_item_id', $invItem->id)
                ->where('branch_id', $branchId)
                ->first();

            if ($stockSummary && $stockSummary->current_stock >= $inventoryQuantity) {
                $this->updateStockByInventoryItem($invItem->id, $inventoryQuantity, $branchId, 'out', $referenceType, $referenceId);
                return;
            }
        }

        // Fallback to recipe
        $recipe = $this->getRecipe($productItemId, $variantId);
        if (!$recipe) return;

        foreach ($recipe->recipeItems as $recipeItem) {
            $netQuantity = $recipeItem->quantity * $quantity;
            $wastage = $recipeItem->wastage_percentage ?? 0;
            $grossQuantity = $wastage < 100 ? ($netQuantity / (1 - ($wastage / 100))) : $netQuantity;

            $invItem = $recipeItem->inventoryItem;
            if (!$invItem) continue;

            if ($invItem->item_type == 1) { // Ingredient
                $deductionQuantity = $this->convertQuantity($grossQuantity, $recipeItem->unit_id, $invItem->unit_id);
                $this->updateStockByInventoryItem($invItem->id, $deductionQuantity, $branchId, 'out', $referenceType, $referenceId);
            } elseif ($invItem->item_type == 3) { // Prep Item (Sub-Product)
                // Recursive deduction
                $this->deductStockForProduct($invItem->reference_id, null, $grossQuantity, $referenceType, $referenceId, $branchId);
            }
        }
    }

    /**
     * Restore stock for a product (Unified).
     */
    public function restoreStockForProduct(int $productItemId, ?int $variantId, float $quantity, string $referenceType, int $referenceId, int $branchId): void
    {
        $product = ProductItem::with('inventoryItem')->find($productItemId);
        if (!$product || !$product->inventoryItem) return;

        $ledgerEntry = StockLedger::where('inventory_item_id', $product->inventoryItem->id)
            ->where('branch_id', $branchId)
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->where('transaction_type', 2)
            ->first();

        if ($ledgerEntry) {
            $invItem = $product->inventoryItem;
            $inventoryQuantity = $this->convertQuantity($quantity, $product->unit_id, $invItem->unit_id);
            $this->updateStockByInventoryItem($invItem->id, $inventoryQuantity, $branchId, 'in', $referenceType, $referenceId);
            return;
        }

        // Fallback to recipe
        $recipe = $this->getRecipe($productItemId, $variantId);
        if (!$recipe) return;

        foreach ($recipe->recipeItems as $recipeItem) {
            $grossQuantity = $recipeItem->quantity * $quantity; 
            
            $invItem = $recipeItem->inventoryItem;
            if (!$invItem) continue;

            if ($invItem->item_type == 1) { // Ingredient
                $this->updateStockByInventoryItem($invItem->id, $grossQuantity, $branchId, 'in', $referenceType, $referenceId);
            } elseif ($invItem->item_type == 3) { // Prep Item (Sub-Product)
                $this->restoreStockForProduct($invItem->reference_id, null, $grossQuantity, $referenceType, $referenceId, $branchId);
            }
        }
    }

    /**
     * Helper to get recipe (specific variant first, then fallback to general).
     */
    protected function getRecipe(int $productItemId, ?int $variantId): ?Recipe
    {
        return Recipe::with(['recipeItems.inventoryItem.unit'])
            ->where('menu_item_id', $productItemId)
            ->where(function($q) use ($variantId) {
                $q->where('variant_id', $variantId)
                  ->orWhereNull('variant_id');
            })
            ->orderByRaw('variant_id IS NULL ASC') // Variant specific first
            ->first();
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
