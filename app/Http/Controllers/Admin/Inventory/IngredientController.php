<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IngredientController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Inventory/Ingredient/Index', [
            'ingredients' => Ingredient::with('unit')->get(),
            'pageTitle' => 'Ingredients'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Inventory/Ingredient/Create', [
            'units' => Unit::where('status', 1)->get(),
            'pageTitle' => 'Add Ingredient'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'min_stock' => 'required|numeric|min:0',
            'expiry_tracking' => 'required|boolean',
            'status' => 'required|boolean'
        ]);

        Ingredient::create($validated);

        return redirect()->route('admin.ingredients.index')->with('success', 'Ingredient created successfully.');
    }

    public function edit(Ingredient $ingredient)
    {
        return Inertia::render('Admin/Inventory/Ingredient/Edit', [
            'ingredient' => $ingredient,
            'units' => Unit::where('status', 1)->get(),
            'pageTitle' => 'Edit Ingredient'
        ]);
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'min_stock' => 'required|numeric|min:0',
            'expiry_tracking' => 'required|boolean',
            'status' => 'required|boolean'
        ]);

        $ingredient->update($validated);

        return redirect()->route('admin.ingredients.index')->with('success', 'Ingredient updated successfully.');
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        return redirect()->route('admin.ingredients.index')->with('success', 'Ingredient deleted successfully.');
    }
}
