<?php

namespace App\Actions\Plan;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class CancelSubscription
{
    public function execute(User $user, StripeClient $stripeClient): void
    {
        if (!empty($user->stripe_plan_id)) {
            $logData = [
                'user_id'                => $user->id,
                'customer'               => $user->stripe_customer_id,
                'stripe_subscription_id' => $user->stripe_plan_id,
                'plan_id'                => $user->plan_id
            ];
            try {
                $stripeClient->subscriptions->cancel($user->stripe_plan_id, []);
                Log::channel('stripe_plans_success')->info('Plan successfully cancelled', $logData);
            } catch (\Exception $e) {
                $logData['error'] = $e->getMessage();
                Log::channel('stripe_plans_success')->error('Plan cancellation error purchased', $logData);
            } finally {
                $user->stripe_plan_id = null;
            }
        }

        $user->plan_id = null;
        $user->plan_until = null;
        $user->plan_from = null;
        $user->saveQuietly();
    }
}
