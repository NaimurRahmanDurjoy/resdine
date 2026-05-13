<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\Account\AccountType;
use App\Models\Account\ChartOfAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChartOfAccountController extends Controller
{
    protected $accountingService;

    public function __construct(\App\Services\AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index()
    {
        $accounts = ChartOfAccount::with(['accountType', 'parent'])
            ->orderBy('code')
            ->get();

        $accountTypes = AccountType::where('status', 1)->get();
        $parentAccounts = ChartOfAccount::where('allow_transaction', 0)->get();

        return Inertia::render('Admin/Accounts/ChartOfAccounts/Index', [
            'accounts' => $accounts,
            'accountTypes' => $accountTypes,
            'parentAccounts' => $parentAccounts,
            'pageTitle' => 'Chart of Accounts'
        ]);
    }

    public function create()
    {
        $accountTypes = AccountType::where('status', 1)->get();
        $parentAccounts = ChartOfAccount::where('allow_transaction', 0)->get();

        return Inertia::render('Admin/Accounts/ChartOfAccounts/Create', [
            'accountTypes' => $accountTypes,
            'parentAccounts' => $parentAccounts,
            'pageTitle' => 'Create New Chart of Account'
        ]);
    }

    public function edit(ChartOfAccount $coa)
    {
        $accountTypes = AccountType::where('status', 1)->get();
        $parentAccounts = ChartOfAccount::where('allow_transaction', 0)
            ->where('id', '!=', $coa->id)
            ->get();

        return Inertia::render('Admin/Accounts/ChartOfAccounts/Edit', [
            'account' => $coa,
            'accountTypes' => $accountTypes,
            'parentAccounts' => $parentAccounts,
            'pageTitle' => 'Edit Chart of Account'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:acc_chart_of_accounts,code',
            'name' => 'required|string',
            'account_type_id' => 'required|exists:acc_account_types,id',
            'parent_id' => 'nullable|exists:acc_chart_of_accounts,id',
            'opening_balance' => 'required|numeric',
            'balance_type' => 'required|in:D,C',
            'allow_transaction' => 'required|boolean',
            'description' => 'nullable|string'
        ]);

        ChartOfAccount::create($validated);

        return back()->with('success', 'Chart of Account created successfully.');
    }

    public function update(Request $request, ChartOfAccount $account)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:acc_chart_of_accounts,code,' . $account->id,
            'name' => 'required|string',
            'account_type_id' => 'required|exists:acc_account_types,id',
            'parent_id' => 'nullable|exists:acc_chart_of_accounts,id',
            'opening_balance' => 'required|numeric',
            'balance_type' => 'required|in:D,C',
            'allow_transaction' => 'required|boolean',
            'description' => 'nullable|string'
        ]);

        $account->update($validated);

        return back()->with('success', 'Chart of Account updated successfully.');
    }

    public function destroy(ChartOfAccount $account)
    {
        if ($account->is_system) {
            return back()->with('error', 'System accounts cannot be deleted.');
        }

        if ($account->voucherDetails()->exists()) {
            return back()->with('error', 'Chart of Account with transactions cannot be deleted.');
        }

        $account->delete();
        return back()->with('success', 'Chart of Account deleted successfully.');
    public function postOpeningBalances()
    {
        try {
            $this->accountingService->generateOpeningBalanceVoucher();
            return back()->with('success', 'Opening balances have been posted to the ledger.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
