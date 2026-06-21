<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $attendances = Attendance::with('employee.user')
            ->whereDate('date', $date)
            ->paginate(20)->withQueryString();

        $employees = Employee::with('user')->orderBy('employee_code')->get();

        return Inertia::render('Admin/HR/Attendance/Index', [
            'attendances' => $attendances,
            'employees' => $employees,
            'date' => $date,
            'pageTitle' => 'Daily Attendance'
        ]);
    }

    public function mark(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|in:Present,Absent,Late,Half-day',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
        ]);

        Attendance::updateOrCreate(
            ['employee_id' => $validated['employee_id'], 'date' => $validated['date']],
            [
                'status' => $validated['status'],
                'check_in' => $validated['check_in'] ?? null,
                'check_out' => $validated['check_out'] ?? null,
            ]
        );

        return back()->with('success', 'Attendance marked successfully.');
    }
}
