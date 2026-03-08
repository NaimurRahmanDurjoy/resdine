<?php

namespace App\Http\Controllers\Admin\RestaurantSetup;

use App\Http\Controllers\Controller;
use App\Models\StaffDepartment;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffDepartmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Settings/RestaurantSetup/StaffDepartments/Index', [
            'departments' => StaffDepartment::with('branch')->get(),
            'pageTitle' => 'Staff Departments'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Settings/RestaurantSetup/StaffDepartments/Create', [
            'branches' => Branch::all(),
            'pageTitle' => 'Add Staff Department'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id'
        ]);

        StaffDepartment::create($validated);

        return redirect()->route('admin.settings.restaurant-setup.staff-departments.index')->with('success', 'Department created successfully.');
    }

    public function edit(StaffDepartment $staffDepartment)
    {
        return Inertia::render('Admin/Settings/RestaurantSetup/StaffDepartments/Edit', [
            'department' => $staffDepartment,
            'branches' => Branch::all(),
            'pageTitle' => 'Edit Staff Department'
        ]);
    }

    public function update(Request $request, StaffDepartment $staffDepartment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id'
        ]);

        $staffDepartment->update($validated);

        return redirect()->route('admin.settings.restaurant-setup.staff-departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(StaffDepartment $staffDepartment)
    {
        $staffDepartment->delete();
        return redirect()->route('admin.settings.restaurant-setup.staff-departments.index')->with('success', 'Department deleted successfully.');
    }
}
