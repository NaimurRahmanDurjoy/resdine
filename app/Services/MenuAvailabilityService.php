<?php

namespace App\Services;

use App\Models\ProductItem;
use App\Models\Recipe;
use App\Models\StockSummary;
use App\Models\ComboItemDetail;
use Illuminate\Support\Facades\Cache;

class MenuAvailabilityService
{
    protected RecipeService $recipeService;

    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    public function isAvailable(int $productItemId, int $branchId, ?int $variantId = null, float $quantity = 1): bool
    {
        return empty($this->recipeService->validateStockForRecipe($productItemId, $variantId, $quantity, $branchId));
    }

    /**
     * Build the availability map for the entire menu using bulk-loaded data.
     * Instead of calling validateStockForRecipe() N times (which fires N*M queries),
     * we load ALL products, recipes, stock summaries, and combo items in ~5 queries,
     * then validate each product in-memory.
     */
    public function getAvailabilityMap(array $productItemIds, int $branchId, ?int $variantId = null, float $quantity = 1): array
    {
        return Cache::remember(
            "menu_availability:{$branchId}",
            60, // seconds
            function () use ($productItemIds, $branchId, $variantId, $quantity) {
                // ── BULK LOAD PHASE (5 queries total) ──

                // 1. Load ALL products with their inventory items (1 query + 1 eager load)
                $products = ProductItem::with('inventoryItem')
                    ->whereIn('id', $productItemIds)
                    ->get()
                    ->keyBy('id');

                // 2. Load ALL stock summaries for this branch (1 query)
                $stockMap = StockSummary::where('branch_id', $branchId)
                    ->get()
                    ->keyBy('inventory_item_id');

                // 3. Load ALL recipes with their items and inventory items (1 query + eager loads)
                $recipes = Recipe::with(['recipeItems.inventoryItem'])
                    ->whereIn('menu_item_id', $productItemIds)
                    ->get();

                // Index recipes: prefer variant-specific, fallback to general
                $recipeMap = [];
                foreach ($recipes as $recipe) {
                    $key = $recipe->menu_item_id;
                    // If we already have a variant-specific recipe for this product, skip general ones
                    if (isset($recipeMap[$key]) && $recipeMap[$key]->variant_id !== null) {
                        continue;
                    }
                    // Prefer variant-specific over general
                    if ($recipe->variant_id === ($variantId ?? null) || !isset($recipeMap[$key])) {
                        $recipeMap[$key] = $recipe;
                    }
                }

                // 4. Load ALL combo item details (1 query)
                $comboItems = ComboItemDetail::whereIn('combo_id', $productItemIds)
                    ->get()
                    ->groupBy('combo_id');

                // ── VALIDATION PHASE (pure in-memory, zero queries) ──

                $availability = [];
                foreach ($productItemIds as $productItemId) {
                    $availability[(int) $productItemId] = $this->checkAvailabilityInMemory(
                        (int) $productItemId,
                        $products,
                        $stockMap,
                        $recipeMap,
                        $comboItems,
                        $quantity
                    );
                }

                return $availability;
            }
        );
    }

    /**
     * Check availability for a single product using pre-loaded data.
     * No database queries are executed in this method.
     */
    protected function checkAvailabilityInMemory(
        int $productItemId,
        $products,
        $stockMap,
        $recipeMap,
        $comboItems,
        float $quantity
    ): bool {
        $product = $products[$productItemId] ?? null;
        if (!$product) return false;

        // 1. Check direct stock (Retail/Prep items with inventory tracking)
        if ($product->inventoryItem) {
            $invItem = $product->inventoryItem;
            $inventoryQuantity = $this->recipeService->convertQuantity($quantity, $product->unit_id, $invItem->unit_id);
            $stock = $stockMap[$invItem->id] ?? null;
            $currentStock = $stock ? (float) $stock->current_stock : 0;

            if ($currentStock >= $inventoryQuantity) {
                return true; // Direct stock is sufficient
            }
        }

        // 2. Handle Combo Items (Type 2)
        if ($product->type == 2) {
            $items = $comboItems[$productItemId] ?? collect();
            if ($items->isEmpty()) return false;

            foreach ($items as $comboItem) {
                $subAvailable = $this->checkAvailabilityInMemory(
                    $comboItem->item_id,
                    $products,
                    $stockMap,
                    $recipeMap,
                    $comboItems,
                    $comboItem->quantity * $quantity
                );
                if (!$subAvailable) return false;
            }
            return true;
        }

        // 3. Fallback to recipe validation (Ingredients)
        $recipe = $recipeMap[$productItemId] ?? null;
        if (!$recipe) return false; // No stock and no recipe = unavailable

        foreach ($recipe->recipeItems as $recipeItem) {
            $invItem = $recipeItem->inventoryItem;
            if (!$invItem) continue;

            if ($invItem->item_type == 1) { // Raw Ingredient
                $netQuantity = $recipeItem->quantity * $quantity;
                $wastage = $recipeItem->wastage_percentage ?? 0;
                $grossQuantity = $wastage < 100 ? ($netQuantity / (1 - ($wastage / 100))) : $netQuantity;

                $requiredQty = $this->recipeService->convertQuantity($grossQuantity, $recipeItem->unit_id, $invItem->unit_id);
                $stock = $stockMap[$invItem->id] ?? null;
                $currentStock = $stock ? (float) $stock->current_stock : 0;

                if ($currentStock < $requiredQty) {
                    return false; // Insufficient ingredient stock
                }
            } elseif ($invItem->item_type == 3) { // Prep Item (Sub-Product)
                // For sub-products, check if their reference product has stock
                $subAvailable = $this->checkAvailabilityInMemory(
                    $invItem->reference_id,
                    $products,
                    $stockMap,
                    $recipeMap,
                    $comboItems,
                    $recipeItem->quantity * $quantity
                );
                if (!$subAvailable) return false;
            }
        }

        return true;
    }

    /**
     * Invalidate the cached availability map for a branch.
     */
    public static function invalidateCache(int $branchId): void
    {
        Cache::forget("menu_availability:{$branchId}");
    }
}
