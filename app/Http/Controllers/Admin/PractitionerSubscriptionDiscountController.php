<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Stripe\CancelSubscriptionDiscount;
use App\Actions\Stripe\CreateOrUpdateSubscriptionDiscount;
use App\Actions\Stripe\UpdateSubscriptionDiscount;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PractitionerSubscriptionDiscountRequest;
use App\Http\Requests\Request;
use App\Models\PractitionerSubscriptionDiscount;
use App\Transformers\PractitionerSubscriptionDiscountTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class PractitionerSubscriptionDiscountController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = PractitionerSubscriptionDiscount::query()
            ->with($request->getIncludes())
            ->paginate($request->getLimit());
        $discounts = $paginator->getCollection();

        return response(
            fractal($discounts, new PractitionerSubscriptionDiscountTransformer())
                ->parseIncludes($request->getIncludes())
                ->toArray()
            )
            ->withPaginationHeaders($paginator);
    }

    public function show(Request $request, PractitionerSubscriptionDiscount $discount)
    {
        return fractal($discount, new PractitionerSubscriptionDiscountTransformer())
            ->parseIncludes($request->getIncludes());
    }

    public function store(PractitionerSubscriptionDiscountRequest $request): JsonResponse
    {
        $discount = run_action(
            CreateOrUpdateSubscriptionDiscount::class,
            $request->getUser(),
            $request->duration_type,
            $request->rate,
            $request->duration_in_months
        );

        return fractal($discount, new PractitionerSubscriptionDiscountTransformer())->respond();
    }

    public function update(
        PractitionerSubscriptionDiscountRequest $request,
        PractitionerSubscriptionDiscount $discount
    ): JsonResponse {
        $updatedDiscount = run_action(
            UpdateSubscriptionDiscount::class,
            $discount,
            $request->duration_type,
            $request->rate,
            $request->duration_in_months
        );

        return fractal($updatedDiscount, new PractitionerSubscriptionDiscountTransformer())->respond();
    }

    public function delete(PractitionerSubscriptionDiscount $discount): Response
    {
        run_action(CancelSubscriptionDiscount::class, $discount);

        return response()->noContent();
    }
}
