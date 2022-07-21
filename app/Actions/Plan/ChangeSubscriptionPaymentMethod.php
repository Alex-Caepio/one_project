<?php

namespace App\Actions\Plan;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class ChangeSubscriptionPaymentMethod
{
    private StripeClient $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    public function execute(User $user, string $paymentMethod): bool
    {
        try {
            DB::beginTransaction();

            $this->updateUserPaymentMethod($user, $paymentMethod);
            $this->updateStripePaymentMethod($user->stripe_plan_id, $paymentMethod);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::channel('stripe_plans_errors')
                ->error('Error purchasing a plan', [
                    'user_id' => $user->id,
                    'stripe_plan_id' => $user->stripe_plan_id,
                    'stripe_payment_method_id' => $paymentMethod,
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

            return false;
        }

        return true;
    }

    /**
     * @param string $planId Stripe's subscription Id of the plan service.
     */
    private function updateStripePaymentMethod(string $planId, string $paymentMethodId): void
    {
        $this->stripe->subscriptions->update(
            $planId,
            ['default_payment_method' => $paymentMethodId],
        );
    }

    private function updateUserPaymentMethod(User $user, string $paymentMethod): User
    {
        $user->default_fee_payment_method = $paymentMethod;
        $user->save();

        return $user;
    }
}
