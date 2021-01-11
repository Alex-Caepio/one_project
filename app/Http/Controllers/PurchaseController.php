<?php

namespace App\Http\Controllers;

use App\Filters\PurchaseFilters;
use App\Http\Requests\Request;
use App\Models\Purchase;
use App\Transformers\PurchaseTransformer;

class PurchaseController extends Controller
{
    public function index(Request $request,PurchaseFilters $filters)
    {
        $Query = Purchase::filter($filters)->where('user_id', $request->user()->id);

        if ($request->hasOrderBy())
        {
            $order = $request->getOrderBy();
            $Query->orderBy($order['column'], $order['direction']);
        }

        $paginator = $Query->paginate();
        $purchases   = $paginator->getCollection();

        return response(fractal($purchases,new PurchaseTransformer())
            ->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);

    }
}
