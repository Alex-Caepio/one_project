<?php


namespace App\Actions\Plan;


use App\Models\Plan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class PlanPurchase
{
    public function execute(Plan $plan, StripeClient $stripe)
    {
        $stripe_id = Auth::user()->stripe_id;
        $plan_id = Auth::user()->plan_id;
        if (!empty($plan_id)) {
            $stripe->subscriptions->cancel(
                $plan_id,
                []
            );
        }
        $subscription = $stripe->subscriptions->create([
            'customer' => $stripe_id,
            'items' => [
                ['plan' => $plan->stripe_id],
            ],
        ]);
        $plan_id = Auth::user();
        $plan_id->plan_id = $subscription->id;
        $unixTimestamp = Carbon::createFromTimestamp($subscription->current_period_end);
        $plan_id->plan_until = $unixTimestamp;
        $plan_id->save();
        return $plan_id;
    }
}
