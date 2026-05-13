<?php

namespace App\Services;

use App\Models\OrderMaster;
use App\Models\OrderPayment;
use App\Models\SalesInvoice;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected $loyaltyService;
    protected $accountingService;

    public function __construct(LoyaltyService $loyaltyService, AccountingService $accountingService)
    {
        $this->loyaltyService = $loyaltyService;
        $this->accountingService = $accountingService;
    }

    /**
     * Process a payment for an order.
     */
    public function processPayment(OrderMaster $order, array $data): OrderPayment
    {
        return DB::transaction(function () use ($order, $data) {
            $amount = $data['amount'];
            
            if ($amount > $order->due_amount && ($data['allow_overpayment'] ?? false) === false) {
                 // Note: In real world, we might allow overpayment for tips, but here we cap it or handle change
                 // For now, let's stick to the current logic
            }

            // 1. Create Payment Record
            $payment = OrderPayment::create([
                'order_master_id' => $order->id,
                'method' => $data['payment_method'],
                'amount' => $amount,
                'payment_reference' => $data['transaction_reference'] ?? null,
                'status' => 1, // Paid
                'paid_at' => now(),
            ]);

            // Handle Loyalty Point Redemption (Method 5)
            if ($data['payment_method'] == 5) {
                if (!$order->member_id) {
                    throw new \Exception("Loyalty point redemption requires a customer.");
                }
                $customer = \App\Models\Customer::find($order->member_id);
                // Assuming 1 point = $1 for simplicity. You can adjust this conversion logic.
                $this->loyaltyService->redeemPoints($customer, $amount);
            }

            // 2. Update Order Master Financials
            $order->due_amount -= $amount;
            $order->collect_amount += $amount;
            
            if ($order->due_amount <= 0) {
                $order->due_amount = 0;
                $order->order_status = 4; // Completed
                
                // Accrue loyalty points only on full payment attainment
                $this->loyaltyService->accruePoints($order);

                // Free the table if occupied
                if ($order->table_id) {
                    \App\Models\RestaurantTable::where('id', $order->table_id)->update(['status' => 1]); // 1 = Free
                }
            }
            $order->save();

            // 3. Update Invoice
            $invoice = $order->invoice;
            if ($invoice) {
                $invoice->collect_amount += $amount;
                $invoice->due_amount -= $amount;
                if ($invoice->due_amount <= 0) {
                    $invoice->due_amount = 0;
                    $invoice->status = 2; // Paid
                }
                $invoice->save();
            }

            // 4. Accounting Posting (Auto-create Receipt Voucher)
            try {
                $this->accountingService->postReceiptForPayment($order, $payment);
            } catch (\Exception $e) {
                \Log::error("Accounting auto-posting failed for order {$order->id}: " . $e->getMessage());
                // We don't throw the exception here to avoid breaking the customer checkout, 
                // but in production, we should have a dashboard to find missing postings.
            }

            return $payment;
        });
    }
}
