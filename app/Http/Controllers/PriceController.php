<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\Price;
use App\Transformers\PriceTransformer;

class PriceController extends Controller
{
    public function update(Request $request, Price $price)
    {
        $price->update($request->all());

        return fractal($price, new PriceTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function destroy(Price $price)
    {
        $price->delete();
        return response(null, 204);
    }
}
