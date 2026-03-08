<?php

namespace App\Http\Controllers\Admin\RestaurantSetup;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TableController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Settings/RestaurantSetup/Tables/Index', [
            'tables' => RestaurantTable::with('branch')->get(),
            'pageTitle' => 'Restaurant Tables'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Settings/RestaurantSetup/Tables/Create', [
            'branches' => Branch::all(),
            'pageTitle' => 'Add Table'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'capacity' => 'required|integer|min:1',
            'section' => 'nullable|string|max:255',
            'status' => 'required|integer'
        ]);

        RestaurantTable::create($validated);

        return redirect()->route('admin.settings.restaurant-setup.tables.index')->with('success', 'Table created successfully.');
    }

    public function edit(RestaurantTable $table)
    {
        return Inertia::render('Admin/Settings/RestaurantSetup/Tables/Edit', [
            'table' => $table,
            'branches' => Branch::all(),
            'pageTitle' => 'Edit Table'
        ]);
    }

    public function update(Request $request, RestaurantTable $table)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'capacity' => 'required|integer|min:1',
            'section' => 'nullable|string|max:255',
            'status' => 'required|integer'
        ]);

        $table->update($validated);

        return redirect()->route('admin.settings.restaurant-setup.tables.index')->with('success', 'Table updated successfully.');
    }

    public function destroy(RestaurantTable $table)
    {
        $table->delete();
        return redirect()->route('admin.settings.restaurant-setup.tables.index')->with('success', 'Table deleted successfully.');
    }
}
