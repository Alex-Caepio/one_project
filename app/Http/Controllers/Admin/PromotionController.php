<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Promo\SavePromotion;
use App\Filters\PromotionFiltrator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Promotion\EnableRequest;
use App\Http\Requests\Promotion\SavePromotionRequest;
use App\Http\Requests\Request;
use App\Models\Promotion;
use App\Transformers\PromotionTransformer;

class PromotionController extends Controller {


    /**
     * @param \App\Http\Requests\Request $request
     * @return mixed
     */
    public function index(Request $request) {

        $promotionQuery = Promotion::query();
        $promotionFilter = new PromotionFiltrator();
        $promotionFilter->apply($promotionQuery, $request);

        $includes = $request->getIncludes();
        $paginator = $promotionQuery->with($includes)->paginate($request->getLimit());

        $promotions = $paginator->getCollection();

        return response(fractal($promotions, new PromotionTransformer())->parseIncludes($includes)->toArray())
            ->withPaginationHeaders($paginator)->withFilters($request)
            ->withCustomInfo(['spend_max_limit' => (int)Promotion::withTrashed()->max('spend_max')]);

    }

    /**
     * @param \App\Models\Promotion $promotionWithTrashed
     * @param \App\Http\Requests\Request $request
     * @return mixed
     */
    public function show(Promotion $promotionWithTrashed, Request $request) {

        return response(fractal($promotionWithTrashed, new PromotionTransformer())
                            ->parseIncludes($request->getIncludes())->toArray());

    }

    /**
     * @param \App\Models\Promotion $promotion
     * @param \App\Http\Requests\Promotion\EnableRequest $request
     * @return mixed
     */
    public function enable(Promotion $promotion, EnableRequest $request) {
        $promotion->status = Promotion::STATUS_ACTIVE;
        $promotion->save();
        return response(fractal($promotion, new PromotionTransformer())->parseIncludes($request->getIncludes())
                                                                       ->toArray());

    }

    /**
     * @param \App\Models\Promotion $promotion
     * @param \App\Http\Requests\Request $request
     * @return mixed
     */
    public function disable(Promotion $promotion, Request $request) {
        $promotion->status = Promotion::STATUS_DISABLED;
        $promotion->save();
        return response(fractal($promotion, new PromotionTransformer())->parseIncludes($request->getIncludes())
                                                                       ->toArray());
    }

    /**
     * @param \App\Models\Promotion $promotion
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Promotion $promotion) {
        $promotion->delete();
        return response(null, 204);
    }

    /**
     * @param \App\Http\Requests\Promotion\SavePromotionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SavePromotionRequest $request) {
        $promotion = run_action(SavePromotion::class, $request);
        return fractal($promotion, new PromotionTransformer())->respond();
    }


    /**
     * @param \App\Models\Promotion $promotion
     * @param \App\Http\Requests\Promotion\SavePromotionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Promotion $promotion, SavePromotionRequest $request) {
        $promotion = run_action(SavePromotion::class, $request, $promotion);
        return fractal($promotion, new PromotionTransformer())->respond();
    }


}
