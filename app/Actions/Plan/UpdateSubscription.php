<?php

namespace App\Actions\Plan;

use App\Events\AccountUpgradedToPractitioner;
use App\Events\ChangeOfSubscription;
use App\Events\SubscriptionConfirmation;
use App\Http\Requests\Request;
use App\Models\Plan;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class UpdateSubscription
{

    public function execute(
        User $user,
        StripeClient $stripeClient,
        Plan $plan,
        bool $isNewPlan,
        $request
    ): bool {
        try {
            $user->plan_id = $plan->id;
            $user->plan_from = Carbon::now();
            $user->account_type = User::ACCOUNT_PRACTITIONER;

            if ($plan->isActiveTrial()) {
                // resubscribe with trial period
                $subscription = $stripeClient->subscriptions->create([
                    'default_payment_method' => $request->payment_method_id,
                    'customer' => $user->stripe_customer_id,
                    'trial_end' => Carbon::now()->addMonths($plan->free_period_length)->timestamp,
                    'items' => [
                        ['price' => $plan->stripe_id],
                    ],
                ]);
            } else {
                // resubscribe without trial period
                $subscription = $stripeClient->subscriptions->create([
                    'default_payment_method' => $request->payment_method_id,
                    'customer' => $user->stripe_customer_id,
                    'items' => [
                        ['price' => $plan->stripe_id],
                    ],
                ]);
            }
            $user->stripe_plan_id = $subscription->id;
            $user->plan_until = Carbon::createFromTimestamp($subscription->current_period_end);


            $isUpgradedToPractitioner = $user->isDirty('account_type');
            if ($isUpgradedToPractitioner) {
                $user->accepted_practitioner_agreement = true;
            }
            $user->save();

            // notifications
            if ($isNewPlan) {
                if ($isUpgradedToPractitioner) {
                    event(new AccountUpgradedToPractitioner($user, $plan));
                }
                event(new SubscriptionConfirmation($user, $plan));
            } else {
                event(new ChangeOfSubscription($user, $plan, null));
            }
        } catch (Exception $e) {
            Log::channel('stripe_plans_errors')
                ->info('Error purchasing a plan', [
                    'user_id' => $user->id ?? null,
                    'plan_id' => $plan->id ?? null,
                    'customer' => $user->stripe_customer_id ?? null,
                    'stripe_plan_id' => $subscription->id ?? null,
                    'payment_method_id' => $request->payment_method_id ?? null,
                    'price_stripe_id' => $plan->stripe_id,
                    'isTrial' => $plan->isActiveTrial(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

            return false;
        }

        Log::channel('stripe_plans_success')
            ->info('Plan successfully purchased', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'customer' => $user->stripe_customer_id,
                'stripe_plan_id' => $subscription->id ?? null,
                'payment_method_id' => $request->payment_method_id ?? null,
                'price_stripe_id' => $plan->stripe_id,
                'isTrial' => $plan->isActiveTrial(),
            ]);

        return true;
    }

}
