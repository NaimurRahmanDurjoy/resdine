<?php

namespace App\Http\Controllers\Admin\RestaurantSetup;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Settings/RestaurantSetup/Branches/Index', [
            'branches' => Branch::all(),
            'pageTitle' => 'Branches'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Settings/RestaurantSetup/Branches/Create', [
            'pageTitle' => 'Add Branch'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'required|integer'
        ]);

        Branch::create($validated);

        return redirect()->route('admin.settings.restaurant-setup.branches.index')->with('success', 'Branch created successfully.');
    }

    public function edit(Branch $branch)
    {
        return Inertia::render('Admin/Settings/RestaurantSetup/Branches/Edit', [
            'branch' => $branch,
            'pageTitle' => 'Edit Branch'
        ]);
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'required|integer'
        ]);

        $branch->update($validated);

        return redirect()->route('admin.settings.restaurant-setup.branches.index')->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('admin.settings.restaurant-setup.branches.index')->with('success', 'Branch deleted successfully.');
    }
}
