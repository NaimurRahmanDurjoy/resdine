<?php

namespace App\Http\Controllers\Admin\RestaurantSetup;

use App\Http\Controllers\Controller;
use App\Models\ResDepartment;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResDepartmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortable = ['name','type','price','status','created_at'];
        $sort = in_array($request->input('sort'), $sortable) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $perPage = min($request->input('perPage', 10), 100);
        $departments = ResDepartment::with('branch')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('branch', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

    return Inertia::render('Admin/Settings/RestaurantSetup/ResDepartments/Index', [
            'departments' => $departments,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'perPage' => $perPage
            ],
            'pageTitle' => 'Restaurant Departments'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Settings/RestaurantSetup/ResDepartments/Create', [
            'branches' => Branch::all(),
            'pageTitle' => 'Add Restaurant Department'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id'
        ]);

        ResDepartment::create($validated);

        return redirect()->route('admin.settings.restaurant-setup.res-departments.index')->with('success', 'Department created successfully.');
    }

    public function edit(ResDepartment $resDepartment)
    {
        return Inertia::render('Admin/Settings/RestaurantSetup/ResDepartments/Edit', [
            'department' => $resDepartment,
            'branches' => Branch::all(),
            'pageTitle' => 'Edit Restaurant Department'
        ]);
    }

    public function update(Request $request, ResDepartment $resDepartment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id'
        ]);

        $resDepartment->update($validated);

        return redirect()->route('admin.settings.restaurant-setup.res-departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(ResDepartment $resDepartment)
    {
        $resDepartment->delete();
        return redirect()->route('admin.settings.restaurant-setup.res-departments.index')->with('success', 'Department deleted successfully.');
    }
}
