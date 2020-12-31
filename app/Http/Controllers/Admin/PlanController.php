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
        $paginator = Plan::with('service_types')->paginate($request->getLimit());
        $plans     = $paginator->getCollection();

        if ($request->hasSearch()) {
            $search = $request->search();

            $plans->where(
                function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('introduction', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                }
            );
        }

        return response(fractal($plans, new PlanTransformer())->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);
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

        $product    = $stripe->products->create([
            'name' => $request->name
        ]);

        $planStripe = $stripe->prices->create([
            'unit_amount'   => $request->get('is_free') ? 0 : $request->get('price'),
            'currency'  => 'usd',
            'recurring' => ['interval' => 'month'],
            'product'   => $product->id
        ]);

        $data              = $request->all();
        $data['stripe_id'] = $planStripe->id;
        $data['order'] = Plan::max('order') + 1;

        $plan->fill($data);
        $plan->save();
        $plan->service_types()->sync($request->get('service_types'));

        return fractal($plan, new PlanTransformer())->respond();
    }

    public function update(Request $request, Plan $plan, StripeClient $stripe)
    {
        $price = $stripe->prices->retrieve($plan->stripe_id);

        $data = $request->all();

        $product_id = $price->product;

        $stripe->products->update($product_id,[
            [
                'name' => $request->name
            ]
        ]);


        $plan->service_types()->sync($request->get('service_types'));
        $plan->update($data);
        return fractal($plan, new PlanTransformer())->respond();
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return response(null, 204);
    }

    public function swapOrder(Plan $firstPlan, Plan $secondPlan)
    {
        $temp = $secondPlan->order;
        $secondPlan->order = $firstPlan->order;
        $firstPlan->order = $temp;

        $firstPlan->save();
        $secondPlan->save();

        return response(200);
    }

}
