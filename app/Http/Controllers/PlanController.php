<?php

namespace App\Http\Controllers;

use App\Events\SubscriptionConfirmationPaid;
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

    public function purchase(Plan $plan, StripeClient $stripe)
    {
        $stripe_id = Auth::user()->stripe_customer_id;
        $plan_id = Auth::user()->plan_id;
        if (!empty($plan_id)) {
            $stripe->subscriptions->cancel(
                $plan_id,
                []
            );
        }

        $card = $stripe->customers->allSources(
            $stripe_id,
            ['object' => 'card', 'limit' => 1]
        );

        if ($card->data[0]->id != null) {

            $subscription = $stripe->subscriptions->create([
                'customer' => $stripe_id,
                'items' => [
                    ['plan' => $plan->stripe_id],
                ],
                'default_payment_method' => $card->data[0]->id,
            ]);
        }  else {
            $subscription = $stripe->subscriptions->create([
                'customer' => $stripe_id,
                'items' => [
                    ['plan' => $plan->stripe_id],
                ],
            ]);
        }

        $plan_id = Auth::user();
        $plan_id->plan_id = $subscription->id;
        $unixTimestamp = Carbon::createFromTimestamp($subscription->current_period_end);
        $plan_id->plan_until = $unixTimestamp;
        $user =  $plan_id->save();

        event(new SubscriptionConfirmationPaid($user));

        return response(null, 204);
    }
}
