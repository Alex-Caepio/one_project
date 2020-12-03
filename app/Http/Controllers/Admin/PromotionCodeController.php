<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PromocodeExport;
use App\Filters\PromocodeFiltrator;
use App\Http\Controllers\Controller;
use App\Models\PromotionCode;
use App\Transformers\PromotionCodeTransformer;
use App\Http\Requests\Request;
use Maatwebsite\Excel\Facades\Excel;

class PromotionCodeController extends Controller {
    public function index(Request $request) {
        $promotionQuery = PromotionCode::query();
        $promotionFilter = new PromocodeFiltrator();
        $promotionFilter->apply($promotionQuery, $request);

        $includes = $request->getIncludes();
        $paginator = $promotionQuery->with($includes)->paginate($request->getLimit());

        $promotions = $paginator->getCollection();
        return fractal($promotions, new PromotionCodeTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    /**
     * @param \App\Http\Requests\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request) {
        $extension = $request->get('type') && $request->get('type') === 'csv' ? 'csv' : 'xlsx';
        return Excel::download(new PromocodeExport($request), 'promocodes.'.$extension);
    }

    public function store(Request $request) {
        $data = $request->all();
        $promotion = PromotionCode::create($data);
        return fractal($promotion, new PromotionCodeTransformer())->respond();
    }

    public function destroy(PromotionCode $promotionCode) {
        $promotionCode->status = PromotionCode::STATUS_DELETED;
        $promotionCode->save();
        $promotionCode->delete();
        return response(null, 204);
    }

    public function update(Request $request, PromotionCode $promotionCode) {
        $promotionCode->update($request->all());
        return fractal($promotionCode, new PromotionCodeTransformer())->respond();
    }

    /**
     * @param \App\Models\PromotionCode $promotionCode
     * @param \App\Http\Requests\Request $request
     * @return mixed
     */
    public function enable(PromotionCode $promotionCode, Request $request) {
        $promotionCode->status = PromotionCode::STATUS_ACTIVE;
        $promotionCode->save();
        return response(fractal($promotionCode, new PromotionCodeTransformer())->toArray());
    }

    /**
     * @param \App\Models\PromotionCode $promotionCode
     * @param \App\Http\Requests\Request $request
     * @return mixed
     */
    public function disable(PromotionCode $promotionCode, Request $request) {
        $promotionCode->status = PromotionCode::STATUS_DISABLED;
        $promotionCode->save();
        return response(fractal($promotionCode, new PromotionCodeTransformer())->toArray());
    }

}
