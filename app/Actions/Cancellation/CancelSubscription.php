<?php

namespace App\Actions\Cancellation;

use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

/**
 * Cancels a subscription. Before cancellation it checks whether the subscription has been canceled.
 */
class CancelSubscription
{
    private const STRIPE_CANCELED_STATUS = 'canceled';

    private StripeClient $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    public function execute(string $subscriptionId): bool
    {
        try {
            if (!$this->hasBeenCanceled($subscriptionId)) {
                $this->stripe->subscriptions->cancel($subscriptionId);
            }
        } catch (Exception $e) {
            Log::channel('stripe_cancel_subscription')
                ->error('Subscription cancel fail: ', [
                    'subscription_id' => $subscriptionId,
                    'message' => $e->getMessage(),
                ])
            ;

            return false;
        }

        Log::channel('stripe_cancel_subscription')
            ->info('Subscription has been canceled: ', [
                'subscription_id' => $subscriptionId,
            ])
        ;

        return true;
    }

    private function hasBeenCanceled(string $subscriptionId): bool
    {
        try {
            $subscription = $this->stripe->subscriptions->retrieve($subscriptionId);
        } catch (Exception $e) {
            Log::channel('stripe_cancel_subscription')
                ->error('Retrive subscription to cancel fail: ', [
                    'subscription_id' => $subscriptionId,
                    'message' => $e->getMessage(),
                ])
            ;

            throw $e;
        }

        return $subscription->status === self::STRIPE_CANCELED_STATUS;
    }
}
