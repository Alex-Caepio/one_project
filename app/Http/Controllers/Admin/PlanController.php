<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlanStoreRequest;
use App\Http\Requests\Admin\PlanUpdateRequest;
use App\Models\Plan;
use App\Transformers\PlanTransformer;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $query = Plan::query();

        if ($request->hasSearch()) {
            $search = $request->search();

            $query->where(
                function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                }
            );
        }

        $includes = $request->getIncludes();
        $includes[] = 'service_types';
        $paginator = $query->with($includes)->paginate($request->getLimit());
        $plans     = $paginator->getCollection();

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

        try {
            $product = $stripe->products->create([
                'name' => $request->name
            ]);

            $planStripe = $stripe->prices->create([
                'unit_amount' => $request->get('is_free') ? 0 : $request->get('price') * 100,
                'currency' => config('app.platform_currency'),
                'recurring' => ['interval' => 'month'],
                'product' => $product->id
            ]);

            $data = $request->all();
            $data['stripe_id'] = $planStripe->id;
            $data['order'] = Plan::max('order') + 1;

            $plan->fill($data);
            $plan->save();
            $plan->service_types()->sync($request->get('service_types'));

        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::channel('stripe_price_error')->info("Client could not purchase plan", [
                'plan_id' => $plan->id,
                'stripe_id'  => $planStripe->id,
                'product'   => $product->id,
                'message' => $e->getMessage(),
            ]);

            return abort(500);
        }

         Log::channel('stripe_price_success')->info("Client purchase plan", [
             'plan_id' => $plan->id,
             'stripe_id'  => $planStripe->id,
             'product'   => $product->id,
         ]);

        return fractal($plan, new PlanTransformer())->respond();
    }

    public function update(PlanUpdateRequest $request, Plan $plan, StripeClient $stripe)
    {
        $price = $stripe->prices->retrieve($plan->stripe_id);

        $data = $request->except(['stripe_id', 'price']);

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
