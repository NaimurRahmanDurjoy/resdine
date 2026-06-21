<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use App\Models\StaffDepartment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::with(['user', 'department'])->paginate(10);
        return Inertia::render('Admin/HR/Employees/Index', [
            'employees' => $employees,
            'pageTitle' => 'Employees List'
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/HR/Employees/Create', [
            'departments' => StaffDepartment::all(),
            'roles' => Role::whereNotIn('name', ['admin', 'customer'])->get(),
            'pageTitle' => 'Add Employee'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'staff_department_id' => 'required|exists:staff_departments,id',
            'employee_code' => 'required|string|unique:employees',
            'designation' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'basic_salary' => 'required|numeric|min:0',
            'joining_date' => 'required|date',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make(12345678), // Default password, should be changed by the user
            'phone' => $validated['phone'],
            'role_id' => $validated['role_id'],
        ]);

        Employee::create([
            'user_id' => $user->id,
            'staff_department_id' => $validated['staff_department_id'],
            'employee_code' => $validated['employee_code'],
            'designation' => $validated['designation'],
            'basic_salary' => $validated['basic_salary'],
            'joining_date' => $validated['joining_date'],
            'status' => 1,
        ]);

        return redirect()->route('admin.hr.employees.index')->with('success', 'Employee created successfully.');
    }
}
