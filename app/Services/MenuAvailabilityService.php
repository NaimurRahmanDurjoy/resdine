<?php

namespace App\Services;

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

    public function getAvailabilityMap(array $productItemIds, int $branchId, ?int $variantId = null, float $quantity = 1): array
    {
        return Cache::remember(
            "menu_availability:{$branchId}",
            60, // seconds
            function () use ($productItemIds, $branchId, $variantId, $quantity) {
                $availability = [];

                foreach ($productItemIds as $productItemId) {
                    $availability[(int) $productItemId] = $this->isAvailable((int) $productItemId, $branchId, $variantId, $quantity);
                }

                return $availability;
            }
        );
    }

    /**
     * Invalidate the cached availability map for a branch.
     */
    public static function invalidateCache(int $branchId): void
    {
        Cache::forget("menu_availability:{$branchId}");
    }
}
