<?php

namespace App\Actions\Plan;

use App\Events\AccountUpgradedToPractitioner;
use App\Events\ChangeOfSubscription;
use App\Events\SubscriptionConfirmation;
use App\Models\Plan;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe\SetupIntent;
use Stripe\Subscription;
use Stripe\StripeClient;

class FinalizeSubscription
{

    public function execute(
        User $user,
        StripeClient $stripeClient,
        Plan $plan,
        bool $isNewPlan,
        $request,
        SetupIntent $intent = null
    ) {
        try {
            $user->plan_id = $plan->id;
            $user->plan_from = Carbon::now();
            $user->account_type = User::ACCOUNT_PRACTITIONER;

            if (empty($intent)) {
                $intent = $stripeClient->setupIntents->retrieve($request->intent_id);
            }

            // return intent
            $subscriptionData = [
                'default_payment_method' => $request->payment_method_id,
                'customer' => $user->stripe_customer_id,
                'items' => [
                    ['price' => $plan->stripe_id],
                ],
            ];

            if ($plan->isActiveTrial()) {
                $subscriptionData['trial_end'] = Carbon::now()->addMonths($plan->free_period_length)->timestamp;

                Log::channel('stripe_plans_info')
                    ->info('Plan is on trial', [
                        'plan_id' => $plan->id ?? null,
                        'stripe_plan_id' => $subscription->id ?? null,
                        'subscription_trial_end' => $subscriptionData['trial_end'],
                        'plan_free_start_from' => $plan->free_start_from,
                        'plan_free_start_to' => $plan->free_start_to,
                        'user_id' => $user->id ?? null,
                        'stripe_customer_id' => $user->stripe_customer_id ?? null,
                        'payment_method_id' => $request->payment_method_id ?? null,
                        'price_stripe_id' => $plan->stripe_id,
                        'subscription_data' => $subscriptionData,
                    ]);
            }

            if (!empty($request->subscription_id)) {
                $subscription = $stripeClient->subscriptions->retrieve($request->subscription_id);
            } else {
                $subscription = $stripeClient->subscriptions->create($subscriptionData);
            }

            $user->stripe_plan_id = $subscription->id;
            $user->plan_until = Carbon::createFromTimestamp($subscription->current_period_end);

            if (in_array($subscription->status, [Subscription::STATUS_INCOMPLETE])) {
                $paymentIntentId = $stripeClient->invoices->retrieve($subscription->latest_invoice)->payment_intent;
                $clientSecret = $stripeClient->paymentIntents->retrieve($paymentIntentId)->client_secret;

                return [
                    'subscription_id' => $subscription->id,
                    'initial_payment' => true,
                    'token' => $clientSecret,
                    'payment_intent_id' => $paymentIntentId,
                ];
            }

            if (in_array($intent->status, [
                SetupIntent::STATUS_REQUIRES_ACTION,
                SetupIntent::STATUS_REQUIRES_CONFIRMATION,
                SetupIntent::STATUS_REQUIRES_PAYMENT_METHOD
            ])) {
                $stripeClient->setupIntents->cancel($intent->id);
            }

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
