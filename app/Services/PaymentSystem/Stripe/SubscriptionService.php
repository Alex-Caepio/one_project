<?php

namespace App\Services\PaymentSystem\Stripe;

use Stripe\StripeClient;

class SubscriptionService
{
    private StripeClient $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    /**
     * Returns subscription invoices by Stripe's subscription ID.
     */
    public function getSubscriptionInvoices(string $id): array
    {
        $data = $this->stripe->invoices->all([
            'subscription' => $id,
        ]);

        return $data->toArray()['data'];
    }
}
