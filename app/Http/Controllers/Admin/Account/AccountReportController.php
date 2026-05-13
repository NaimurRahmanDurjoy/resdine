<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\Account\ChartOfAccount;
use App\Models\Account\VoucherDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AccountReportController extends Controller
{
    public function generalLedger(Request $request)
    {
        $accounts = ChartOfAccount::orderBy('code')->get();
        
        $selectedAccount = $request->account_id;
        $fromDate = $request->from_date ?? now()->startOfMonth()->toDateString();
        $toDate = $request->to_date ?? now()->toDateString();

        $ledger = [];
        $openingBalance = 0;

        if ($selectedAccount) {
            $account = ChartOfAccount::findOrFail($selectedAccount);
            
            // 1. Calculate Opening Balance (Account Opening + Previous Transactions)
            $prevDebit = VoucherDetail::where('chart_of_account_id', $selectedAccount)
                ->whereHas('voucher', function($q) use ($fromDate) {
                    $q->where('voucher_date', '<', $fromDate)->where('status', 2);
                })->sum('debit');

            $prevCredit = VoucherDetail::where('chart_of_account_id', $selectedAccount)
                ->whereHas('voucher', function($q) use ($fromDate) {
                    $q->where('voucher_date', '<', $fromDate)->where('status', 2);
                })->sum('credit');

            // Account's base opening balance
            $baseOpening = $account->opening_balance;
            if ($account->balance_type === 'C') {
                $openingBalance = $baseOpening + ($prevCredit - $prevDebit);
            } else {
                $openingBalance = $baseOpening + ($prevDebit - $prevCredit);
            }

            // 2. Fetch Ledger Transactions
            $ledger = VoucherDetail::with(['voucher.voucherType'])
                ->where('chart_of_account_id', $selectedAccount)
                ->whereHas('voucher', function($q) use ($fromDate, $toDate) {
                    $q->whereBetween('voucher_date', [$fromDate, $toDate])
                      ->where('status', 2);
                })
                ->join('acc_vouchers', 'acc_voucher_details.voucher_id', '=', 'acc_vouchers.id')
                ->orderBy('acc_vouchers.voucher_date')
                ->orderBy('acc_vouchers.id')
                ->select('acc_voucher_details.*')
                ->get();
        }

        return Inertia::render('Admin/Accounts/Reports/GeneralLedger', [
            'accounts' => $accounts,
            'ledger' => $ledger,
            'openingBalance' => (float)$openingBalance,
            'filters' => [
                'account_id' => $selectedAccount,
                'from_date' => $fromDate,
                'to_date' => $toDate
            ],
            'pageTitle' => 'General Ledger'
        ]);
    }

    public function trialBalance(Request $request)
    {
        $asOfDate = $request->as_of_date ?? now()->toDateString();

        $accounts = ChartOfAccount::with(['accountType'])
            ->orderBy('code')
            ->get()
            ->map(function ($account) use ($asOfDate) {
                // Calculate Net Movement
                $totals = DB::table('acc_voucher_details')
                    ->join('acc_vouchers', 'acc_voucher_details.voucher_id', '=', 'acc_vouchers.id')
                    ->where('acc_voucher_details.chart_of_account_id', $account->id)
                    ->where('acc_vouchers.voucher_date', '<=', $asOfDate)
                    ->where('acc_vouchers.status', 2) // Approved only
                    ->select(
                        DB::raw('SUM(debit) as total_debit'),
                        DB::raw('SUM(credit) as total_credit')
                    )
                    ->first();

                $netMovement = ($totals->total_debit ?? 0) - ($totals->total_credit ?? 0);
                
                // Adjust for Opening Balance Nature
                if ($account->balance_type === 'C') {
                    $balance = $account->opening_balance + ($totals->total_credit ?? 0) - ($totals->total_debit ?? 0);
                    $debit = $balance < 0 ? abs($balance) : 0;
                    $credit = $balance >= 0 ? $balance : 0;
                } else {
                    $balance = $account->opening_balance + ($totals->total_debit ?? 0) - ($totals->total_credit ?? 0);
                    $debit = $balance >= 0 ? $balance : 0;
                    $credit = $balance < 0 ? abs($balance) : 0;
                }

                return [
                    'id' => $account->id,
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->accountType->name,
                    'debit' => (float)$debit,
                    'credit' => (float)$credit,
                ];
            });

        return Inertia::render('Admin/Accounts/Reports/TrialBalance', [
            'data' => $accounts,
            'filters' => ['as_of_date' => $asOfDate],
            'pageTitle' => 'Trial Balance'
        ]);
    }

    public function profitAndLoss(Request $request)
    {
        $fromDate = $request->from_date ?? now()->startOfMonth()->toDateString();
        $toDate = $request->to_date ?? now()->toDateString();

        // 1. Fetch Revenue Accounts (Account Type ID 4 is usually Revenue)
        $revenueAccounts = ChartOfAccount::where('account_type_id', 4)
            ->get()
            ->map(function ($account) use ($fromDate, $toDate) {
                $account->balance = DB::table('acc_voucher_details')
                    ->join('acc_vouchers', 'acc_voucher_details.voucher_id', '=', 'acc_vouchers.id')
                    ->where('acc_voucher_details.chart_of_account_id', $account->id)
                    ->whereBetween('acc_vouchers.voucher_date', [$fromDate, $toDate])
                    ->where('acc_vouchers.status', 2)
                    ->select(DB::raw('SUM(credit) - SUM(debit) as balance'))
                    ->value('balance') ?? 0;
                return $account;
            })->filter(fn($a) => $a->balance != 0);

        // 2. Fetch Expense Accounts (Account Type ID 5 is usually Expense)
        $expenseAccounts = ChartOfAccount::where('account_type_id', 5)
            ->get()
            ->map(function ($account) use ($fromDate, $toDate) {
                $account->balance = DB::table('acc_voucher_details')
                    ->join('acc_vouchers', 'acc_voucher_details.voucher_id', '=', 'acc_vouchers.id')
                    ->where('acc_voucher_details.chart_of_account_id', $account->id)
                    ->whereBetween('acc_vouchers.voucher_date', [$fromDate, $toDate])
                    ->where('acc_vouchers.status', 2)
                    ->select(DB::raw('SUM(debit) - SUM(credit) as balance'))
                    ->value('balance') ?? 0;
                return $account;
            })->filter(fn($a) => $a->balance != 0);

        $totalRevenue = $revenueAccounts->sum('balance');
        $totalExpense = $expenseAccounts->sum('balance');

        return Inertia::render('Admin/Accounts/Reports/ProfitAndLoss', [
            'revenueAccounts' => $revenueAccounts,
            'expenseAccounts' => $expenseAccounts,
            'totalRevenue' => (float)$totalRevenue,
            'totalExpense' => (float)$totalExpense,
            'netProfit' => (float)($totalRevenue - $totalExpense),
            'filters' => [
                'from_date' => $fromDate,
                'to_date' => $toDate
            ],
            'pageTitle' => 'Profit & Loss Statement'
        ]);
    }
}
