<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
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

    public function updateStatus(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            'status' => 'required|in:Approved,Rejected'
        ]);

        $leave->update(['status' => $validated['status']]);

        return back()->with('success', 'Leave status updated.');
    }
}
