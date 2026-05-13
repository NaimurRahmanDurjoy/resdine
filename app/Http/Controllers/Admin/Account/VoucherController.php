<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\Account\ChartOfAccount;
use App\Models\Account\Voucher;
use App\Models\Account\VoucherType;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VoucherController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index()
    {
        $vouchers = Voucher::with(['voucherType', 'branch', 'creator'])
            ->orderBy('voucher_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Accounts/Vouchers/Index', [
            'vouchers' => $vouchers,
            'pageTitle' => 'Account Vouchers'
        ]);
    }

    public function create()
    {
        $voucherTypes = VoucherType::where('status', 1)->get();
        $accounts = ChartOfAccount::where('allow_transaction', 1)
            ->where('status', 1)
            ->orderBy('code')
            ->get();

        return Inertia::render('Admin/Accounts/Vouchers/Create', [
            'voucherTypes' => $voucherTypes,
            'accounts' => $accounts,
            'pageTitle' => 'New Voucher Entry'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'voucher_type_id' => 'required|exists:acc_voucher_types,id',
            'voucher_date' => 'required|date',
            'reference_no' => 'nullable|string',
            'narration' => 'nullable|string',
            'details' => 'required|array|min:2',
            'details.*.chart_of_account_id' => 'required|exists:acc_chart_of_accounts,id',
            'details.*.debit' => 'required|numeric|min:0',
            'details.*.credit' => 'required|numeric|min:0',
            'details.*.narration' => 'nullable|string',
        ]);

        try {
            $this->accountingService->createVoucher($validated);
            return redirect()->route('admin.accounts.vouchers.index')->with('success', 'Voucher created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Voucher $voucher)
    {
        $voucher->load(['details.chartOfAccount', 'voucherType', 'branch', 'creator', 'approver', 'sources']);
        
        return Inertia::render('Admin/Accounts/Vouchers/Show', [
            'voucher' => $voucher,
            'pageTitle' => 'Voucher Details'
        ]);
    }

    public function approve(Voucher $voucher)
    {
        try {
            $this->accountingService->approveVoucher($voucher);
            return back()->with('success', 'Voucher approved and posted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
