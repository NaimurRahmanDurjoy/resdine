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
        $perPage = min($request->input('perPage', 10), 100);

        $recipes = Recipe::with(['menuItem:id,name', 'recipeItems.ingredient:id,name', 'recipeItems.unit:id,name'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('menuItem', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Admin/Recipes/Index', [
            'recipes' => $recipes,
            'filters' => [
                'search' => $search,
                'perPage' => $perPage
            ],
            'pageTitle' => 'Recipes'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Recipes/Create', [
            'menuItems' => ProductItem::where('status', 1)->whereDoesntHave('recipe')->get(['id', 'name']),
            'ingredients' => Ingredient::with('unit')->where('status', 1)->get(),
            'units' => Unit::all(),
            'branches' => Branch::all(),
            'pageTitle' => 'Create Recipe'
        ]);
    }

    public function store(StoreRecipeRequest $request)
    {
        $this->recipeService->storeOrUpdate($request->validated());

        return redirect()->route('admin.recipes.index')->with('success', 'Recipe created successfully.');
    }

    public function edit(Recipe $recipe)
    {
        return Inertia::render('Admin/Recipes/Edit', [
            'recipe' => $recipe->load(['recipeItems.ingredient.unit', 'menuItem']),
            'menuItems' => ProductItem::where('id', $recipe->menu_item_id)->get(['id', 'name']), // Only show the current item
            'ingredients' => Ingredient::with('unit')->where('status', 1)->get(),
            'units' => Unit::all(),
            'branches' => Branch::all(),
            'pageTitle' => 'Edit Recipe: ' . $recipe->menuItem->name
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
