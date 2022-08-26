<?php

namespace App\Actions\Plan;

use App\Models\Plan;
use App\Models\User;
use Stripe\SetupIntent;
use Stripe\StripeClient;

class UpdateSubscription
{
    public function execute(
        User $user,
        StripeClient $stripeClient,
        Plan $plan,
        bool $isNewPlan,
        $request
    ): array {
        $intent = null;

        if (empty($request->intent_id) && $request->is_final == false) {
            $intent = $stripeClient->setupIntents->create(
                [
                    'customer' => $user->stripe_customer_id,
                    'payment_method' => $request->payment_method_id,
                    'payment_method_types' => ['card'],
                ]
            );

            $intent = $stripeClient->setupIntents->confirm(
                $intent->id,
                ['payment_method' => $request->payment_method_id]
            );

            if ($intent->status !== SetupIntent::STATUS_SUCCEEDED) {
                return [
                    'status' => $intent->status,
                    'token' => $intent->client_secret,
                    'id' => $intent->id
                ];
            }

            $request['intent_id'] = $intent->id;
        }

        $finalize = run_action(
            FinalizeSubscription::class,
            $user,
            $stripeClient,
            $plan,
            $isNewPlan,
            $request,
            $intent,
        );

        if (is_array($finalize)) {
            return $finalize;
        }

        return [
            'status' => $finalize !== false ? 'succeeded' : 'error'
        ];
    }
}
