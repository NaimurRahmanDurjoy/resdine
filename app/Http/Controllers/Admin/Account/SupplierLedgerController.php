<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Exception;

class SupplierLedgerController extends Controller
{
    public function index()
    {
        // Get all suppliers with their latest balance
        $suppliers = Supplier::with(['ledgers' => function($q) {
            $q->orderBy('created_at', 'desc')->take(1);
        }])->paginate(10);

        // Calculate current balances dynamically if needed, or rely on the latest ledger entry
        foreach ($suppliers as $supplier) {
            $supplier->current_balance = $supplier->ledgers->first()->balance ?? 0;
        }

        return Inertia::render('Admin/Accounts/SupplierLedger/Index', [
            'suppliers' => $suppliers,
            'pageTitle' => 'Supplier Balances'
        ]);
    }

    public function show(Supplier $supplier)
    {
        $ledgers = SupplierLedger::where('supplier_id', $supplier->id)
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return Inertia::render('Admin/Accounts/SupplierLedger/Show', [
            'supplier' => $supplier,
            'ledgers' => $ledgers,
            'pageTitle' => 'Statement: ' . $supplier->name
        ]);
    }

    public function storePayment(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($validated, $supplier) {
                $lastLedger = SupplierLedger::where('supplier_id', $supplier->id)
                    ->orderBy('transaction_date', 'desc')
                    ->orderBy('id', 'desc')
                    ->lockForUpdate()
                    ->first();

                $previousBalance = $lastLedger ? $lastLedger->balance : 0;
                $newBalance = $previousBalance - $validated['amount']; // Payments reduce the balance owed

                SupplierLedger::create([
                    'supplier_id' => $supplier->id,
                    'reference_type' => 'payment',
                    'dr_amount' => $validated['amount'],
                    'cr_amount' => 0,
                    'balance' => $newBalance,
                    'transaction_date' => $validated['transaction_date'],
                    'notes' => $validated['notes']
                ]);
            });

            return back()->with('success', 'Payment recorded successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
