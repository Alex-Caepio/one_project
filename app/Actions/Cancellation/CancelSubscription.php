<?php

namespace App\Actions\Cancellation;

use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class CancelSubscription
{
    private StripeClient $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    public function execute(string $subscriptionId): void
    {
        try {
            $this->stripe->subscriptions->cancel($subscriptionId);
        } catch (\Exception $e) {
            Log::channel('stripe_refund_fail')
                ->error('Subscription cancel fail: ', [
                    'subscription_id' => $subscriptionId,
                    'message' => $e->getMessage(),
                ])
            ;
        }
    }
}
