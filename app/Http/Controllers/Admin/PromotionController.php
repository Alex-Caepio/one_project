<?php

namespace App\Http\Controllers\Admin;

use App\Filters\PromotionFiltrator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use App\Models\Discipline;
use App\Models\FocusArea;
use App\Models\Promotion;
use App\Models\PromotionCode;
use App\Models\ServiceType;
use App\Models\User;
use App\Transformers\PromotionTransformer;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
            ->withCustomInfo(['spend_max_limit' => Promotion::max('spend_max')]);

    }

    /**
     * @param \App\Models\Promotion $promotion
     * @param \App\Http\Requests\Request $request
     * @return mixed
     */
    public function show(Promotion $promotion, Request $request) {

        return response(fractal($promotion, new PromotionTransformer())->parseIncludes($request->getIncludes())
                                                                       ->toArray());

    }


    /**
     * @param \App\Models\Promotion $promotion
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Promotion $promotion) {
        $promotion->delete();
        return response(null, 204);
    }

}
