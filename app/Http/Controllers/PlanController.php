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
        $stripe_id = Auth::user()->stripe_customer_id;
        $plan_id   = Auth::user()->plan_id;
        if (!empty($plan_id)) {
            $stripe->subscriptions->cancel($plan_id, []);
        }

        $subscription = $stripe->subscriptions->create([
            'default_payment_method' => $request->payment_method_id,
            'customer'               => $stripe_id,
            'items'                  => [
                ['plan' => $plan->stripe_id],
            ],
        ]);

        $user             = Auth::user();
        $user->plan_id    = $subscription->id;
        $user->plan_until = Carbon::createFromTimestamp($subscription->current_period_end);;
        $user->save();

        event(new SubscriptionConfirmationPaid($user));

        return response(null, 204);
    }
}
