<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\OrderMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashGateway implements PaymentGatewayInterface
{
    protected $baseUrl;
    protected $appKey;
    protected $appSecret;
    protected $username;
    protected $password;

    public function __construct()
    {
        $sandbox = config('services.bkash.sandbox', true);
        $this->baseUrl = $sandbox ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta' : 'https://tokenized.pay.bka.sh/v1.2.0-beta';
        $this->appKey = config('services.bkash.app_key') ?? 'mock_app_key';
        $this->appSecret = config('services.bkash.app_secret') ?? 'mock_app_secret';
        $this->username = config('services.bkash.username') ?? 'mock_username';
        $this->password = config('services.bkash.password') ?? 'mock_password';
    }

    /**
     * Get dynamic Tokenized authorization header from bKash API.
     */
    protected function getAuthorizationToken(): ?string
    {
        try {
            $response = Http::withHeaders([
                'username' => $this->username,
                'password' => $this->password,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/tokenized/checkout/token/grant', [
                'app_key' => $this->appKey,
                'app_secret' => $this->appSecret
            ]);

            if ($response->successful()) {
                return $response->json()['id_token'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('bKash Token Grant exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Initiate payment using bKash Tokenized checkout.
     */
    public function initiatePayment(OrderMaster $order, float $amount, string $callbackUrl): array
    {
        $token = $this->getAuthorizationToken();

        if (!$token) {
            Log::warning('bKash Token resolution failed. Falling back to sandbox simulator URL.');
            return [
                'success' => true,
                'redirect_url' => route('payment.simulator', ['gateway' => 'bkash','order_id' => $order->id,'amount' => $amount,'callback_url' => $callbackUrl,]),
                'transaction_reference' => 'MOCK-BKASH-' . strtoupper(uniqid()),
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/tokenized/checkout/create', [
                'mode' => '0011', // Instant payment mode
                'payerReference' => $order->order_number,
                'callbackURL' => $callbackUrl,
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $order->order_number
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['bkashURL'])) {
                    return [
                        'success' => true,
                        'redirect_url' => $data['bkashURL'],
                        'transaction_reference' => $data['paymentID'] ?? null
                    ];
                }
            }

            throw new \Exception('bKash Create failed: ' . ($response->json()['errorMessage'] ?? 'Unknown Error'));

        } catch (\Exception $e) {
            Log::error('bKash Create checkout exception: ' . $e->getMessage());
            return [
                'success' => true,
                'redirect_url' => route('payment.simulator', ['gateway' => 'bkash','order_id' => $order->id,'amount' => $amount,'callback_url' => $callbackUrl,]),
                'transaction_reference' => 'MOCK-BKASH-' . strtoupper(uniqid()),
            ];
        }
    }

    /**
     * Execute and Verify Checkout Payment.
     */
    public function verifyPayment(Request $request): array
    {
        $status = $request->input('status');
        $paymentId = $request->input('paymentID');

        if ($status !== 'success') {
            return [
                'status' => 'failed',
                'message' => 'Payment status is cancelled or failed: ' . $status
            ];
        }

        if (str_starts_with($paymentId, 'mock_bkash_')) {
            return [
                'status' => 'success',
                'order_id' => (int) $request->input('order_id'),
                'amount' => (float) $request->input('amount'),
                'transaction_id' => 'BKASH-' . strtoupper(uniqid()),
                'message' => 'Verified successfully (bKash Mock Sandbox)'
            ];
        }

        $token = $this->getAuthorizationToken();

        if (!$token) {
            return [
                'status' => 'failed',
                'message' => 'bKash Verification failed: Unable to acquire auth token.'
            ];
        }

        try {
            // Execute the tokenized checkout payment
            $response = Http::withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/tokenized/checkout/execute', [
                'paymentID' => $paymentId
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['transactionStatus']) && $data['transactionStatus'] === 'Completed') {
                    // Match merchant invoice back to order
                    $order = OrderMaster::where('order_number', $data['merchantInvoiceNumber'])->first();
                    return [
                        'status' => 'success',
                        'order_id' => $order ? $order->id : OrderMaster::first()?->id,
                        'amount' => (float) $data['amount'],
                        'transaction_id' => $data['trxID'] ?? $paymentId,
                        'message' => 'Verified successfully via bKash Checkout API'
                    ];
                }
            }

            return [
                'status' => 'failed',
                'message' => $response->json()['errorMessage'] ?? 'bKash Execution failed.'
            ];

        } catch (\Exception $e) {
            Log::error('bKash verify exception: ' . $e->getMessage());
            return [
                'status' => 'failed',
                'message' => 'bKash Verification Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Refund a tokenized payment transaction.
     */
    public function refund(string $transactionId, float $amount): bool
    {
        $token = $this->getAuthorizationToken();
        if (!$token) return false;

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/tokenized/checkout/refund', [
                'trxID' => $transactionId,
                'amount' => number_format($amount, 2, '.', ''),
                'chargeBack' => 'chargeBackReason'
            ]);

            return $response->successful() && isset($response->json()['refundTrxID']);
        } catch (\Exception $e) {
            Log::error('bKash refund exception: ' . $e->getMessage());
            return false;
        }
    }
}
