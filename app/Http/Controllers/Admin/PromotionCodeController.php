<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromotionCode;
use App\Transformers\PromotionCodeTransformer;
use App\Http\Requests\Request;

class PromotionCodeController extends Controller
{
    public function index(Request $request)
    {
        $promotion = PromotionCode::all();
        return fractal($promotion, new PromotionCodeTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $promotion = PromotionCode::create($data);
        return fractal($promotion, new PromotionCodeTransformer())->respond();
    }

    public function destroy(PromotionCode $promotionCode)
    {
        $promotionCode->delete();
        return response(null, 204);
    }

    public function update(Request $request, PromotionCode $promotionCode)
    {
        $promotionCode->update($request->all());
        return fractal($promotionCode, new PromotionCodeTransformer())->respond();
    }
}
