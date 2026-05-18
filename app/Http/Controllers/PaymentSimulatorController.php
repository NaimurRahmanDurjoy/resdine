<?php

namespace App\Http\Controllers;

use App\Models\OrderMaster;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentSimulatorController extends Controller
{
    /**
     * Render the payment simulator panel.
     */
    public function showSimulator(Request $request, string $gateway)
    {
        $orderId = $request->input('order_id');
        $amount = $request->input('amount', 10.00);
        $callbackUrl = $request->input('callback_url');

        $order = OrderMaster::find($orderId);
        $orderNumber = $order ? $order->order_number : ('ORD-SIM-' . strtoupper(uniqid()));

        // Fetch dynamic currency symbol
        $branchSetting = null;
        if ($order && $order->branch_id) {
            $branchSetting = \App\Models\BranchSetting::with('currency')->where('branch_id', $order->branch_id)->first();
        }
        $currencySymbol = $branchSetting?->currency?->symbol ?? '$';

        return Inertia::render('Web/PaymentSimulator', [
            'gateway' => strtolower($gateway),
            'orderId' => $orderId,
            'orderNumber' => $orderNumber,
            'amount' => (float) $amount,
            'currency' => $currencySymbol,
            'callbackUrl' => $callbackUrl,
        ]);
    }
}
