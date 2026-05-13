<?php

namespace App\Services;

use App\Models\Account\ChartOfAccount;
use App\Models\Account\FiscalYear;
use App\Models\Account\Voucher;
use App\Models\Account\VoucherType;
use Illuminate\Support\Facades\DB;
use Exception;

class AccountingService
{
    /**
     * Create a balanced accounting voucher.
     */
    public function generateOpeningBalanceVoucher()
    {
        return DB::transaction(function () {
            // 1. Find or create Opening Balance Equity account
            $equity = ChartOfAccount::firstOrCreate(
                ['code' => '3000'],
                [
                    'name' => 'Opening Balance Equity',
                    'account_type_id' => 3, // Equity
                    'allow_transaction' => 1,
                    'balance_type' => 'C',
                    'status' => 1
                ]
            );

            // 2. Get all accounts with opening balances
            $accounts = ChartOfAccount::where('opening_balance', '>', 0)
                ->where('id', '!=', $equity->id)
                ->get();

            if ($accounts->isEmpty()) {
                throw new \Exception("No accounts found with an opening balance.");
            }

            // 3. Create the Master Voucher
            $voucher = Voucher::create([
                'voucher_no' => 'OB-' . date('YmdHis'),
                'voucher_type_id' => 1, // Journal
                'voucher_date' => now()->startOfYear(),
                'narration' => 'Opening Balance Migration',
                'status' => 2, // Automatically Approve
                'created_by' => auth()->id(),
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($accounts as $acc) {
                $isDebit = $acc->balance_type === 'D';
                $debit = $isDebit ? $acc->opening_balance : 0;
                $credit = !$isDebit ? $acc->opening_balance : 0;

                VoucherDetail::create([
                    'voucher_id' => $voucher->id,
                    'chart_of_account_id' => $acc->id,
                    'debit' => $debit,
                    'credit' => $credit,
                    'narration' => 'Initial Opening Balance'
                ]);

                $totalDebit += $debit;
                $totalCredit += $credit;
            }

            // 4. Balance it against Equity
            $equityDiff = $totalDebit - $totalCredit;
            VoucherDetail::create([
                'voucher_id' => $voucher->id,
                'chart_of_account_id' => $equity->id,
                'debit' => $equityDiff < 0 ? abs($equityDiff) : 0,
                'credit' => $equityDiff > 0 ? $equityDiff : 0,
                'narration' => 'Opening Balance Contra'
            ]);

            $voucher->update([
                'total_debit' => max($totalDebit, $totalCredit),
                'total_credit' => max($totalDebit, $totalCredit)
            ]);

            return $voucher;
        });
    }

    public function approveVoucher(Voucher $voucher)
    {
        if ($voucher->status !== 1) {
            throw new \Exception("Only Draft vouchers can be approved.");
        }

        $voucher->update([
            'status' => 2, // Approved
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        return $voucher;
    }

    public function createVoucher(array $data)
    {
        return DB::transaction(function () use ($data) {
            $fiscalYear = FiscalYear::active();
            if (!$fiscalYear) {
                throw new Exception("No active fiscal year found.");
            }

            $type = VoucherType::findOrFail($data['voucher_type_id']);
            $voucherNo = $this->generateVoucherNo($type);

            $totalDebit = collect($data['details'])->sum('debit');
            $totalCredit = collect($data['details'])->sum('credit');

            if (abs($totalDebit - $totalCredit) > 0.001) {
                throw new Exception("Voucher is not balanced. Debits ({$totalDebit}) must equal Credits ({$totalCredit}).");
            }

            $voucher = Voucher::create([
                'branch_id' => $data['branch_id'] ?? auth()->user()->branch_id,
                'voucher_no' => $voucherNo,
                'voucher_type_id' => $type->id,
                'fiscal_year_id' => $fiscalYear->id,
                'voucher_date' => $data['voucher_date'] ?? now(),
                'reference_no' => $data['reference_no'] ?? null,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'narration' => $data['narration'] ?? null,
                'status' => $data['status'] ?? 1, // Default to draft
                'created_by' => auth()->id(),
            ]);

            // Handle Source Linking (Decoupled Architecture)
            if (!empty($data['source_id']) && !empty($data['source_type'])) {
                \App\Models\Account\VoucherSource::create([
                    'voucher_id' => $voucher->id,
                    'source_type' => $data['source_type'],
                    'source_id' => $data['source_id'],
                    'metadata' => $data['source_metadata'] ?? null,
                ]);
            }

            foreach ($data['details'] as $detail) {
                $voucher->details()->create([
                    'chart_of_account_id' => $detail['chart_of_account_id'],
                    'debit' => $detail['debit'] ?? 0,
                    'credit' => $detail['credit'] ?? 0,
                    'narration' => $detail['narration'] ?? null,
                ]);
            }

            return $voucher;
        });
    }

    /**
     * Helper to find an account by its unique code.
     */
    public function getAccountByCode(string $code)
    {
        return ChartOfAccount::where('code', $code)->first();
    }

    /**
     * Generate the next voucher number based on type prefix and starting number.
     */
    protected function generateVoucherNo(VoucherType $type)
    {
        $count = Voucher::where('voucher_type_id', $type->id)->count();
        $nextNumber = $type->starting_number + $count;
        return $type->prefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Auto-post a Receipt Voucher for a POS Payment.
     */
    public function postReceiptForPayment($order, $payment)
    {
        $cashAccount = $this->getAccountByCode('1001'); // Standard Cash Account
        $salesAccount = $this->getAccountByCode('4001'); // Standard Food Sales Account

        if (!$cashAccount || !$salesAccount) {
            // Log warning or throw error if system accounts are missing
            return null;
        }

        return $this->createVoucher([
            'voucher_type_id' => 2, // Receipt Voucher
            'branch_id' => $order->branch_id,
            'voucher_date' => $payment->paid_at ?? now(),
            'reference_no' => $order->order_number,
            'source_id' => $payment->id,
            'source_type' => \App\Models\Account\VoucherSource::TYPE_POS_PAYMENT,
            'narration' => "Sales Receipt for Order #{$order->order_number}",
            'status' => 2, // Auto-approve system entries
            'details' => [
                [
                    'chart_of_account_id' => $cashAccount->id,
                    'debit' => $payment->amount,
                    'credit' => 0,
                    'narration' => "Cash received for Order #{$order->order_number}"
                ],
                [
                    'chart_of_account_id' => $salesAccount->id,
                    'debit' => 0,
                    'credit' => $payment->amount,
                    'narration' => "Revenue recognized for Order #{$order->order_number}"
                ]
            ]
        ]);
    }
}
