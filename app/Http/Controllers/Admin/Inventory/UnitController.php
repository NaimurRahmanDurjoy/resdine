<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UnitController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Inventory/Unit/Index', [
            'units' => Unit::all(),
            'pageTitle' => 'Units'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Inventory/Unit/Create', [
            'pageTitle' => 'Add Unit'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'status' => 'required|integer'
        ]);

        Unit::create($validated);

        return redirect()->route('admin.unit.index')->with('success', 'Unit created successfully.');
    }

    public function edit(Unit $unit)
    {
        return Inertia::render('Admin/Inventory/Unit/Edit', [
            'unit' => $unit,
            'pageTitle' => 'Edit Unit'
        ]);
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'status' => 'required|integer'
        ]);

        $unit->update($validated);

        return redirect()->route('admin.unit.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('admin.unit.index')->with('success', 'Unit deleted successfully.');
    }
}
