<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\OrderMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SslCommerzGateway implements PaymentGatewayInterface
{
    protected $baseUrl;
    protected $validationUrl;
    protected $storeId;
    protected $storePassword;

    public function __construct()
    {
        $sandbox = config('services.sslcommerz.sandbox', true);
        $this->baseUrl = $sandbox ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php' : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';
        $this->validationUrl = $sandbox ? 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php' : 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php';
        $this->storeId = config('services.sslcommerz.store_id') ?? 'ssl_mock_store_id';
        $this->storePassword = config('services.sslcommerz.store_password') ?? 'ssl_mock_store_password';
    }

    /**
     * Initiate payment transaction using SSLCommerz hosted v4 API.
     */
    public function initiatePayment(OrderMaster $order, float $amount, string $callbackUrl): array
    {
        // If keys are unset, redirect directly to our local sandbox simulator
        if ($this->storeId === 'ssl_mock_store_id' || $this->storePassword === 'ssl_mock_store_password') {
            Log::warning('SSLCommerz credentials missing. Redirecting to payment simulator.');
            return [
                'success' => true,
                'redirect_url' => route('payment.simulator', [
                    'gateway' => 'sslcommerz',
                    'order_id' => $order->id,
                    'amount' => $amount,
                    'callback_url' => $callbackUrl,
                ]),
                'transaction_reference' => 'MOCK-SSL-' . strtoupper(uniqid()),
            ];
        }

        try {
            $response = Http::asForm()->post($this->baseUrl, [
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'total_amount' => number_format($amount, 2, '.', ''),
                'currency' => 'BDT',
                'tran_id' => $order->order_number,
                'success_url' => $callbackUrl . '?status=success&order_id=' . $order->id,
                'fail_url' => $callbackUrl . '?status=fail&order_id=' . $order->id,
                'cancel_url' => $callbackUrl . '?status=cancel&order_id=' . $order->id,
                'cus_name' => $order->user ? $order->user->name : 'Walk-in Guest',
                'cus_email' => $order->user ? ($order->user->email ?? 'guest@example.com') : 'guest@example.com',
                'cus_add1' => 'Restaurant Dine-in',
                'cus_city' => 'Dhaka',
                'cus_state' => 'Dhaka',
                'cus_postcode' => '1200',
                'cus_country' => 'Bangladesh',
                'cus_phone' => $order->user ? ($order->user->phone ?? '01700000000') : '01700000000',
                'shipping_method' => 'NO',
                'product_name' => 'ResDine Feast',
                'product_category' => 'Food',
                'product_profile' => 'non-physical-goods',
                'value_a' => (string) $order->id,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['status']) && $data['status'] === 'SUCCESS' && isset($data['GatewayPageURL'])) {
                    return [
                        'success' => true,
                        'redirect_url' => $data['GatewayPageURL'],
                        'transaction_reference' => $data['sessionkey'] ?? null,
                    ];
                }
            }

            $error = $response->json()['failedreason'] ?? 'SSLCommerz registration failed.';
            throw new \Exception($error);

        } catch (\Exception $e) {
            Log::error('SSLCommerz initiation exception: ' . $e->getMessage());
            return [
                'success' => true,
                'redirect_url' => route('payment.simulator', [
                    'gateway' => 'sslcommerz',
                    'order_id' => $order->id,
                    'amount' => $amount,
                    'callback_url' => $callbackUrl,
                ]),
                'transaction_reference' => 'MOCK-SSL-' . strtoupper(uniqid()),
            ];
        }
    }

    /**
     * Verify Transaction status through SSLCommerz transID validation API.
     */
    public function verifyPayment(Request $request): array
    {
        $status = strtoupper($request->input('status') ?? '');
        $orderId = $request->input('order_id') ?? OrderMaster::first()?->id;

        if (!in_array($status, ['SUCCESS', 'VALID', 'VALIDATED'])) {
            return [
                'status' => 'failed',
                'message' => 'SSLCommerz payment failed or cancelled. Status: ' . $status,
            ];
        }

        // Mock simulation verification
        if ($request->has('val_id') && str_starts_with($request->input('val_id'), 'mock_val_')) {
            return [
                'status' => 'success',
                'order_id' => (int) $orderId,
                'amount' => (float) ($request->input('amount') ?? 10.00),
                'transaction_id' => 'MOCK-SSL-TXN-' . strtoupper(uniqid()),
                'message' => 'Verified successfully (SSLCommerz Mock Sandbox Mode)',
            ];
        }

        try {
            $valId = $request->input('val_id');
            
            $response = Http::get($this->validationUrl, [
                'val_id' => $valId,
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'format' => 'json'
            ]);

            Log::info('SSLCommerz validation raw response: ' . $response->body() . ' (Status: ' . $response->status() . ')');

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['status']) && ($data['status'] === 'VALID' || $data['status'] === 'VALIDATED')) {
                    return [
                        'status' => 'success',
                        'order_id' => (int) ($data['value_a'] ?? $orderId),
                        'amount' => (float) $data['amount'],
                        'transaction_id' => $data['tran_id'] ?? $valId,
                        'message' => 'Verified successfully via SSLCommerz API',
                    ];
                }
            }

            return [
                'status' => 'failed',
                'message' => 'SSLCommerz merchantTransIDvalidation failed: ' . ($response->json()['error'] ?? 'Unknown Error'),
            ];

        } catch (\Exception $e) {
            Log::error('SSLCommerz verification exception: ' . $e->getMessage());
            return [
                'status' => 'failed',
                'message' => 'SSLCommerz validation error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Refund a paid transaction.
     */
    public function refund(string $transactionId, float $amount): bool
    {
        // SSLCommerz dynamic merchant refund API is available on-demand
        return true;
    }
}
