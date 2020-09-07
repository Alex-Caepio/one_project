<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Transformers\PlanTransformer;
use App\Http\Requests\Request;
use Stripe\StripeClient;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $plans = Plan::with('service_types')->get();
        return fractal($plans, new PlanTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }
    public function show(Plan $plan,Request $request)
    {
        return fractal($plan, new PlanTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }
    public function store(Request $request, StripeClient $stripe)
    {
        $plan = new Plan();
        $planStripe = $stripe->plans->create([
            'amount' => $request->get('amount'),
            'currency' => 'usd',
            'interval' => 'month',
            'product' => ['name' => $request->get('name')],
        ]);
        $plan->forceFill([
            'name' => $request->get('name'),
            'stripe_id' => $planStripe->id,
            'price' => $planStripe->amount,
        ]);
        $plan->save();
        $plan->service_types()->sync($request->get('service_types'));
        return fractal($plan, new PlanTransformer())->respond();
    }
    public function update(Request $request, Plan $plan)
    {
        $plan->update($request->all());
        return fractal($plan, new PlanTransformer())->respond();
    }
    public function destroy(Plan $plan)
    {
        $plan->delete();
        return response(null, 204);
    }
}
