<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortable = ['name','short_name','status','created_at'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $perPage = min($request->input('perPage', 10), 100);
        $units = Unit::with('baseUnit')
                    ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orderBy($sort, $direction)
                    ->paginate($perPage)
                    ->withQueryString();

        return Inertia::render('Admin/Inventory/Unit/Index', [
            'units' => $units,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'perPage' => $perPage
            ],
            'pageTitle' => 'Units'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Inventory/Unit/Create', [
            'baseUnits' => Unit::whereNull('base_unit_id')->where('status', 1)->get(['id', 'name']),
            'pageTitle' => 'Add Unit'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_unit_id' => 'nullable|exists:units,id',
            'conversion_factor' => 'nullable|numeric|min:0.000001',
            'status' => 'required|integer'
        ]);

        Unit::create($validated);

        return redirect()->route('admin.unit.index')->with('success', 'Unit created successfully.');
    }

    public function edit(Unit $unit)
    {
        return Inertia::render('Admin/Inventory/Unit/Edit', [
            'unit' => $unit,
            'baseUnits' => Unit::whereNull('base_unit_id')->where('id', '!=', $unit->id)->where('status', 1)->get(['id', 'name']),
            'pageTitle' => 'Edit Unit'
        ]);
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_unit_id' => 'nullable|exists:units,id',
            'conversion_factor' => 'nullable|numeric|min:0.000001',
            'status' => 'required|integer'
        ]);

        // Prevent circular reference: a base unit cannot have another units as base if it's already a base for something else
        if ($validated['base_unit_id'] && $unit->subUnits()->exists()) {
             return back()->withErrors(['base_unit_id' => 'This unit is already a base for other units and cannot be a sub-unit.']);
        }

        $unit->update($validated);

        return redirect()->route('admin.unit.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('admin.unit.index')->with('success', 'Unit deleted successfully.');
    }
}
