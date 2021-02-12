<?php

namespace App\Http\Controllers;

use App\Events\SubscriptionConfirmationPaid;
use App\Http\Requests\Plans\PlanRequest;
use App\Models\Plan;
use App\Http\Requests\Request;
use App\Transformers\PlanTransformer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $plans = Plan::with('service_types')->get();
        return fractal($plans, new PlanTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function purchase(Plan $plan, StripeClient $stripe, PlanRequest $request)
    {
        $user             = Auth::user();

        try {
            $subscription = $stripe->subscriptions->create([
                'default_payment_method' => $request->payment_method_id,
                'customer'               => $user->stripe_customer_id,
                'items'                  => [
                    ['price' => $plan->stripe_id],
                ],
            ]);

            if ($subscription->id) {
                if (!empty($user->stripe_plan_id)) {
                    $stripe->subscriptions->cancel($user->stripe_plan_id, []);
                }
                $user->stripe_plan_id = $subscription->id;
                $user->plan_id    = $plan->id;
            }

            $user->plan_until = Carbon::createFromTimestamp($subscription->current_period_end);;
            $user->plan_from  = Carbon::now();
            $user->account_type = 'practitioner';
            $user->save();

        } catch (\Stripe\Exception\ApiErrorException $e) {

            Log::channel('stripe_plans_errors')->info('Error purchasing a plan', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'customer'  => $user->stripe_customer_id,
                'stripe_plan_id' => $subscription->id,
                'payment_method_id' => $request->payment_method_id,
                'price_stripe_id' => $plan->stripe_id,
                'message' => $e->getMessage(),
            ]);


            return response()->json(['payment_method_id' => 'could not process that payment method'], 422);
        }

        Log::channel('stripe_plans_success')->info('Plan successfully purchased', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'customer'  => $user->stripe_customer_id,
            'stripe_plan_id' => $subscription->id,
            'payment_method_id' => $request->payment_method_id,
            'price_stripe_id' => $plan->stripe_id
        ]);

//        event(new SubscriptionConfirmationPaid($user));

        return response(null, 204);
    }
}
