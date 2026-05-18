<?php

namespace App\Contracts;

use App\Models\OrderMaster;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Initiate a payment and return the checkout redirect URL or intent keys.
     *
     * @param OrderMaster $order
     * @param float $amount
     * @param string $callbackUrl
     * @return array
     */
    public function initiatePayment(OrderMaster $order, float $amount, string $callbackUrl): array;

    /**
     * Verify payment status following return from checkout redirect.
     *
     * @param Request $request
     * @return array
     */
    public function verifyPayment(Request $request): array;

    /**
     * Refund a paid transaction through the gateway.
     *
     * @param string $transactionId
     * @param float $amount
     * @return bool
     */
    public function refund(string $transactionId, float $amount): bool;
}
