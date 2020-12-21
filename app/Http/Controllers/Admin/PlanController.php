<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlanStoreRequest;
use App\Models\Plan;
use App\Transformers\PlanTransformer;
use App\Http\Requests\Request;
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

    public function show(Plan $plan, Request $request)
    {
        return fractal($plan, new PlanTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function store(PlanStoreRequest $request, StripeClient $stripe)
    {
        $plan       = new Plan();
        $planStripe = $stripe->plans->create([
            'amount'   => $request->get('is_free') ? 0 : $request->get('price'),
            'currency' => 'usd',
            'interval' => 'month',
            'product'  => ['name' => $request->get('name')],
        ]);

        $data              = $request->all();
        $data['stripe_id'] = $planStripe->id;

        $plan->fill($data);
        $plan->save();
        $plan->service_types()->sync($request->get('service_types'));

        return fractal($plan, new PlanTransformer())->respond();
    }

    public function update(Request $request, Plan $plan)
    {
        $plan->update($request->all());
        $plan->service_types()->sync($request->get('service_types'));

        return fractal($plan, new PlanTransformer())->respond();
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return response(null, 204);
    }
}
