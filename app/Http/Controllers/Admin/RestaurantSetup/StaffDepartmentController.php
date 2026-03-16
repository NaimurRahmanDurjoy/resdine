<?php

namespace App\Http\Controllers\Admin\RestaurantSetup;

use App\Http\Controllers\Controller;
use App\Models\StaffDepartment;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffDepartmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortable = ['name','type','price','status','created_at'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $perPage = min($request->input('perPage', 10), 100);
        $departments = StaffDepartment::with('branch')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('branch', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->appends(request()->query());

    return Inertia::render('Admin/Settings/RestaurantSetup/StaffDepartments/Index', [
            'departments' => $departments,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'perPage' => $perPage
            ],
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
