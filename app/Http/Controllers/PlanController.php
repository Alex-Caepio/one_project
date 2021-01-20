<?php

namespace App\Http\Controllers;

use App\Events\SubscriptionConfirmationPaid;
use App\Http\Requests\Plans\PlanRequest;
use App\Models\Plan;
use App\Http\Requests\Request;
use App\Transformers\PlanTransformer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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
            }

            $user->plan_id    = $plan->id;
            $user->plan_until = Carbon::createFromTimestamp($subscription->current_period_end);;
            $user->plan_from  = Carbon::now();
            $user->save();

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['payment_method_id' => 'could not process that payment method'], 422);
        }

        event(new SubscriptionConfirmationPaid($user));

        return response(null, 204);
    }
}
