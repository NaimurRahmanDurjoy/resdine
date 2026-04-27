<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recipe\StoreRecipeRequest;
use App\Models\Recipe;
use App\Models\ProductItem;
use App\Models\Ingredient;
use App\Models\Unit;
use App\Models\Branch;
use App\Services\RecipeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RecipeController extends Controller
{
    protected $recipeService;

    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $menuItemId = $request->input('menu_item_id');
        $perPage = min($request->input('perPage', 10), 100);

        $recipes = Recipe::with(['menuItem:id,name,price', 'variant:id,name,price', 'recipeItems.ingredient:id,name', 'recipeItems.subProduct:id,name', 'recipeItems.unit:id,name'])
            ->when($menuItemId, function ($query) use ($menuItemId) {
                $query->where('menu_item_id', $menuItemId);
            })
            ->when($search, function ($query) use ($search) {
                $query->whereHas('menuItem', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('variant', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        // Transform to include cost and GP%
        $recipes->getCollection()->transform(function ($recipe) {
            $costData = $this->recipeService->calculateRecipeCost($recipe);
            $sellingPrice = $recipe->variant_id ? ($recipe->variant->price ?? $recipe->menuItem->price) : $recipe->menuItem->price;
            
            $recipe->food_cost = $costData['total_cost'];
            $recipe->selling_price = $sellingPrice;
            $recipe->gp_percentage = $sellingPrice > 0 ? round((($sellingPrice - $costData['total_cost']) / $sellingPrice) * 100, 2) : 0;
            return $recipe;
        });

        return Inertia::render('Admin/Recipes/Index', [
            'recipes' => $recipes,
            'filters' => [
                'search' => $search,
                'perPage' => $perPage
            ],
            'pageTitle' => 'Recipes'
        ]);
    }

    public function create(Request $request)
    {
        $selectedProductId = $request->get('product_id');
        
        $ingredients = Ingredient::with('unit')->where('status', 1)->get()->map(function($ing) {
            $ing->latest_cost = $this->recipeService->getLatestIngredientCost($ing->id, $ing->unit_id);
            return $ing;
        });

        $prepItems = ProductItem::prepItems()->where('status', 1)->get(['id', 'name', 'unit_id']);
        $prepItems->transform(function($item) {
            $recipe = $item->recipe;
            $item->latest_cost = $recipe ? $this->recipeService->calculateRecipeCost($recipe)['total_cost'] : 0;
            return $item;
        });

        return Inertia::render('Admin/Recipes/Create', [
            'menuItems' => ProductItem::with('variants')->where('status', 1)->get(['id', 'name', 'price']),
            'prepItems' => $prepItems,
            'ingredients' => $ingredients,
            'units' => Unit::all(),
            'branches' => Branch::all(),
            'initialProductId' => $selectedProductId,
            'pageTitle' => 'Create Recipe'
        ]);
    }

    public function store(StoreRecipeRequest $request)
    {
        $this->recipeService->storeOrUpdate($request->validated());

        return redirect()->route('admin.recipes.index')->with('success', 'Recipe saved successfully.');
    }

    public function edit(Recipe $recipe)
    {
        $ingredients = Ingredient::with('unit')->where('status', 1)->get()->map(function($ing) {
            $ing->latest_cost = $this->recipeService->getLatestIngredientCost($ing->id, $ing->unit_id);
            return $ing;
        });

        $prepItems = ProductItem::prepItems()->where('status', 1)->get(['id', 'name', 'unit_id']);
        $prepItems->transform(function($item) {
            $recipe = $item->recipe;
            $item->latest_cost = $recipe ? $this->recipeService->calculateRecipeCost($recipe)['total_cost'] : 0;
            return $item;
        });

        return Inertia::render('Admin/Recipes/Edit', [
            'recipe' => $recipe->load(['recipeItems.ingredient.unit', 'recipeItems.subProduct.recipe', 'menuItem.variants', 'variant']),
            'menuItems' => ProductItem::with('variants')->where('status', 1)->get(['id', 'name', 'price']),
            'prepItems' => $prepItems,
            'ingredients' => $ingredients,
            'units' => Unit::all(),
            'branches' => Branch::all(),
            'pageTitle' => 'Edit Recipe'
        ]);
    }

    public function update(StoreRecipeRequest $request, Recipe $recipe)
    {
        $this->recipeService->storeOrUpdate($request->validated());

        return redirect()->route('admin.recipes.index')->with('success', 'Recipe updated successfully.');
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->recipeItems()->delete();
        $recipe->delete();

        return redirect()->route('admin.recipes.index')->with('success', 'Recipe deleted successfully.');
    }
}
