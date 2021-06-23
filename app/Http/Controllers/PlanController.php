<?php

namespace App\Http\Controllers;

use App\Events\AccountUpgradedToPractitioner;
use App\Events\ChangeOfSubscription;
use App\Helpers\UserRightsHelper;
use App\Http\Requests\Plans\PlanRequest;
use App\Events\SubscriptionConfirmation;
use App\Models\Article;
use App\Models\Plan;
use App\Http\Requests\Request;
use App\Models\User;
use App\Transformers\PlanTransformer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class PlanController extends Controller {
    public function index(Request $request) {
        $plans = Plan::with('service_types')->get();
        return fractal($plans, new PlanTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function purchase(Plan $plan, StripeClient $stripe, PlanRequest $request) {
        $user = Auth::user();
        $previousPlan = $user->plan;
        $isNewPlan = empty($user->plan_id);


        if (!empty($user->stripe_plan_id)) {
            $logData = [
                'user_id'                => $user->id,
                'plan_id'                => $plan->id,
                'customer'               => $user->stripe_customer_id,
                'stripe_subscription_id' => $user->stripe_plan_id,
                'price_stripe_id'        => $plan->stripe_id
            ];
            try {
                $stripe->subscriptions->cancel($user->stripe_plan_id, []);
                Log::channel('stripe_plans_success')->info('Plan successfully cancelled', $logData);
            } catch (\Exception $e) {
                $logData['error'] = $e->getMessage();
                Log::channel('stripe_plans_success')->info('Plan cancellation error purchased', $logData);
            } finally {
                $user->stripe_plan_id = null;
                $user->plan_id = null;
                $user->plan_until = null;
                $user->plan_from = null;
                $user->save();
            }
        }

        try {

            $subscription = $stripe->subscriptions->create([
                                                               'default_payment_method' => $request->payment_method_id,
                                                               'customer'               => $user->stripe_customer_id,
                                                               'items'                  => [
                                                                   ['price' => $plan->stripe_id],
                                                               ],
                                                           ]);
            $user->stripe_plan_id = $subscription->id;
            $user->plan_id = $plan->id;
            $user->plan_until = Carbon::createFromTimestamp($subscription->current_period_end);
            $user->plan_from = Carbon::now();
            $user->account_type = User::ACCOUNT_PRACTITIONER;
            $isUpgradedToPractitioner = $user->isDirty('account_type');
            if ($isUpgradedToPractitioner) {
                $user->accepted_practitioner_agreement = true;
            }
            $user->save();

            if ($isNewPlan) {
                if ($isUpgradedToPractitioner) {
                    event(new AccountUpgradedToPractitioner($user, $plan));
                }
                event(new SubscriptionConfirmation($user, $plan));
            } else {
                event(new ChangeOfSubscription($user, $plan, $previousPlan));
            }

        } catch (\Exception $e) {
            Log::channel('stripe_plans_errors')->info('Error purchasing a plan', [
                'user_id'           => $user->id ?? null,
                'plan_id'           => $plan->id ?? null,
                'customer'          => $user->stripe_customer_id ?? null,
                'stripe_plan_id'    => $subscription->id ?? null,
                'payment_method_id' => $request->payment_method_id ?? null,
                'price_stripe_id'   => $plan->stripe_id ?? null,
                'message'           => $e->getMessage(),
                'trace'             => $e->getTraceAsString(),
            ]);


            return response()->json([
                                        'errors' => [
                                            'payment_method_id' => 'The payment could not be processed. Please check with your bank or choose another payment option.'
                                        ]
                                    ], 422);
        }

        Log::channel('stripe_plans_success')->info('Plan successfully purchased', [
            'user_id'           => $user->id,
            'plan_id'           => $plan->id,
            'customer'          => $user->stripe_customer_id,
            'stripe_plan_id'    => $subscription->id,
            'payment_method_id' => $request->payment_method_id,
            'price_stripe_id'   => $plan->stripe_id
        ]);
        return response('', 204);

    }
}
