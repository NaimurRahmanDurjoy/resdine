<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::with('employee.user')->orderBy('created_at', 'desc')->paginate(10);
        return Inertia::render('Admin/HR/Leaves/Index', [
            'leaves' => $leaves,
            'pageTitle' => 'Leave Requests'
        ]);
    }

    public function create()
    {
        $employees = Employee::with('user')->orderBy('employee_code')->get();
        return Inertia::render('Admin/HR/Leaves/Create', [
            'employees' => $employees,
            'pageTitle' => 'Request Leave'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|string|max:255',
            'reason' => 'required|string|max:1000',
        ]);

        Leave::create([
            'employee_id' => $validated['employee_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'type' => $validated['type'],
            'reason' => $validated['reason'],
            'status' => 'Pending',
        ]);

        return redirect()->route('admin.hr.leaves.index')->with('success', 'Leave request created successfully.');
    }

    public function updateStatus(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            'status' => 'required|in:Approved,Rejected'
        ]);

        $leave->update(['status' => $validated['status']]);

        return back()->with('success', 'Leave status updated.');
    }
}
