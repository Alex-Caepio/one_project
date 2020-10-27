<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Http\Requests\Request;
use App\Actions\Plan\PlanPurchase;
use App\Transformers\PlanTransformer;
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
        $user = run_action(PlanPurchase::class, $plan, $stripe);
        event(new SubscriptionConfirmationPaid($user));

        return response(null, 204);
    }

}
