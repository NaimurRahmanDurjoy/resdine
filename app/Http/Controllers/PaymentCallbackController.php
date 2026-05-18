<?php

namespace App\Http\Controllers;

use App\Services\Payments\PaymentManager;
use App\Services\PaymentService;
use App\Models\OrderMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    protected $paymentManager;
    protected $paymentService;

    public function __construct(PaymentManager $paymentManager, PaymentService $paymentService)
    {
        $this->paymentManager = $paymentManager;
        $this->paymentService = $paymentService;
    }

    /**
     * Handle redirected transaction verification from external payment gateway.
     */
    public function handleCallback(Request $request, string $gatewayName)
    {
        Log::info("Payment callback received for gateway: {$gatewayName}", $request->all());

        try {
            $gateway = $this->paymentManager->driver($gatewayName);
            $result = $gateway->verifyPayment($request);

            if ($result['status'] === 'success') {
                $order = OrderMaster::findOrFail($result['order_id']);

                // Complete the order payment transaction
                $this->paymentService->processPayment($order, [
                    'amount' => $result['amount'],
                    'payment_method' => $gatewayName === 'bkash' ? 3 : 2, // 3 = Mobile Banking, 2 = Card/Stripe
                    'transaction_reference' => $result['transaction_id'],
                    'allow_overpayment' => true,
                ]);

                return redirect()->route('web.menu')->with('success', 'Thank you! Your payment was verified successfully and your order is placed.');
            }

            Log::warning("Payment verification failed for gateway {$gatewayName}: " . ($result['message'] ?? 'Unspecified error'));
            return redirect()->route('web.menu')->with('error', 'Payment verification failed: ' . ($result['message'] ?? 'Unknown gateway verification error.'));

        } catch (\Exception $e) {
            Log::error("Payment callback processing exception: " . $e->getMessage());
            return redirect()->route('web.menu')->with('error', 'Payment exception occurred: ' . $e->getMessage());
        }
    }
}
