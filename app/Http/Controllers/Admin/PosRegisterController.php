<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosRegister;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PosRegisterController extends Controller
{
    /**
     * Show the open shift screen.
     */
    public function showOpen()
    {
        // Check if there's already an open register for this user/branch
        $openRegister = PosRegister::where('user_id', auth()->id())
            ->where('branch_id', auth()->user()->branch_id)
            ->where('status', 1)
            ->first();

        if ($openRegister) {
            return redirect()->route('admin.pos.index');
        }

        return Inertia::render('Admin/Pos/OpenShift', [
            'pageTitle' => 'Open Register Shift'
        ]);
    }

    /**
     * Process opening a shift.
     */
    public function open(Request $request)
    {
        $request->validate([
            'opening_cash' => 'required|numeric|min:0'
        ]);

        PosRegister::create([
            'user_id' => auth()->id(),
            'branch_id' => auth()->user()->branch_id ?? 1,
            'opened_at' => now(),
            'opening_cash' => $request->opening_cash,
            'expected_cash' => $request->opening_cash,
            'status' => 1 // Open
        ]);

        return redirect()->route('admin.pos.index')->with('success', 'Shift opened successfully.');
    }

    /**
     * Show the close shift screen.
     */
    public function showClose()
    {
        $register = PosRegister::where('user_id', auth()->id())
            ->where('branch_id', auth()->user()->branch_id)
            ->where('status', 1)
            ->firstOrFail();

        // Calculate expected cash
        // expected_cash = opening_cash + total_cash_payments
        $cashPayments = DB::table('order_payments')
            ->join('order_masters', 'order_payments.order_master_id', '=', 'order_masters.id')
            ->where('order_masters.register_id', $register->id)
            ->where('order_payments.method', 1) // Cash
            ->where('order_payments.status', 1) // Completed
            ->sum('order_payments.amount');

        $register->expected_cash = $register->opening_cash + $cashPayments;

        return Inertia::render('Admin/Pos/CloseShift', [
            'register' => $register,
            'pageTitle' => 'Close Register Shift'
        ]);
    }

    /**
     * Process closing a shift.
     */
    public function close(Request $request)
    {
        $request->validate([
            'closing_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $register = PosRegister::where('user_id', auth()->id())
            ->where('branch_id', auth()->user()->branch_id)
            ->where('status', 1)
            ->firstOrFail();

        $register->update([
            'closed_at' => now(),
            'closing_cash' => $request->closing_cash,
            'notes' => $request->notes,
            'status' => 0 // Closed
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Shift closed and register reconciled.');
    }
}
