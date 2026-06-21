<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with('employee.user')->orderBy('created_at', 'desc')->paginate(10);
        $employees = Employee::with('user')->orderBy('employee_code')->get();

        return Inertia::render('Admin/HR/Payroll/Index', [
            'payrolls' => $payrolls,
            'employees' => $employees,
            'pageTitle' => 'Payroll Processing'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|string',
            'year' => 'required|digits:4',
            'allowances' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $net_salary = $employee->basic_salary + $validated['allowances'] - $validated['deductions'];

        Payroll::create([
            'employee_id' => $employee->id,
            'month' => $validated['month'],
            'year' => $validated['year'],
            'basic_salary' => $employee->basic_salary,
            'allowances' => $validated['allowances'],
            'deductions' => $validated['deductions'],
            'net_salary' => $net_salary,
            'payment_status' => 'Paid'
        ]);

        return back()->with('success', 'Payroll generated successfully.');
    }
}
