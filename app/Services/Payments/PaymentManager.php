<?php

namespace App\Services\Payments;

use Illuminate\Support\Manager;

class PaymentManager extends Manager
{
    /**
     * Get the default payment driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config->get('services.payment.default') ?? 'stripe';
    }

    /**
     * Resolve the Stripe driver instance.
     *
     * @return StripeGateway
     */
    public function createStripeDriver()
    {
        return new StripeGateway();
    }

    /**
     * Resolve the bKash driver instance.
     *
     * @return BkashGateway
     */
    public function createBkashDriver()
    {
        return new BkashGateway();
    }

    /**
     * Resolve the SSLCommerz driver instance.
     *
     * @return SslCommerzGateway
     */
    public function createSslcommerzDriver()
    {
        return new SslCommerzGateway();
    }
}
