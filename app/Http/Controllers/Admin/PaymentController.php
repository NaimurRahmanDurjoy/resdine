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
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(Request $request, $orderId)
    {
        $request->validate([
            'payment_method' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'transaction_reference' => 'nullable|string',
        ]);

        try {
            $order = OrderMaster::findOrFail($orderId);

            if ($request->amount > $order->due_amount) {
                return back()->withErrors(['amount' => 'Payment amount exceeds due amount.']);
            }

            $this->paymentService->processPayment($order, [
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'transaction_reference' => $request->transaction_reference,
            ]);

            return back()->with('success', 'Payment processed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to process payment: ' . $e->getMessage()]);
        }
    }
}
