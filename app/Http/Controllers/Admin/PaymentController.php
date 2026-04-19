<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderMaster;
use App\Models\OrderPayment;
use App\Models\SalesInvoice;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    public function store(Request $request, $orderId)
    {
        $request->validate([
            'payment_method' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'transaction_reference' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $orderId) {
            $order = OrderMaster::findOrFail($orderId);

            if ($request->amount > $order->due_amount) {
                return back()->withErrors(['amount' => 'Payment amount exceeds due amount.']);
            }

            OrderPayment::create([
                'order_master_id' => $order->id,
                'method' => $request->payment_method,
                'amount' => $request->amount,
                'payment_reference' => $request->transaction_reference,
                'status' => 1, // Paid
                'paid_at' => now(),
            ]);

            $order->due_amount -= $request->amount;
            $order->collect_amount += $request->amount;
            
            if ($order->due_amount <= 0) {
                $order->order_status = 4; // Completed
                
                // Accrue loyalty points on full payment
                $this->loyaltyService->accruePoints($order);
            }
            $order->save();

            // Update Invoice
            $invoice = SalesInvoice::where('order_id', $order->id)->first();
            if ($invoice) {
                $invoice->collect_amount += $request->amount;
                $invoice->due_amount -= $request->amount;
                if ($invoice->due_amount <= 0) {
                    $invoice->status = 2; // Paid
                }
                $invoice->save();
            }

            return back()->with('success', 'Payment processed successfully.');
        });
    }
}
