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
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;
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

        $finalize = run_action(
            FinalizeSubscription::class,
            $user,
            $stripeClient,
            $plan,
            $isNewPlan,
            $request
        );

        return [
            'status' => $finalize ? 'succeeded' : 'error'
        ];
    }

}
