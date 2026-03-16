<?php

namespace App\Http\Controllers\Admin\RestaurantSetup;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortable = ['name','type','price','status','created_at'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $perPage = min($request->input('perPage', 10), 100);
        $tables = RestaurantTable::with('branch')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('branch', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->appends(request()->query());
    return Inertia::render('Admin/Settings/RestaurantSetup/Tables/Index', [
            'tables' => $tables,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'perPage' => $perPage
            ],
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
