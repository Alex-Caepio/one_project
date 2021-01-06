<?php

namespace App\Http\Controllers;

use App\Filters\PurchaseFilters;
use App\Http\Requests\Request;
use App\Models\Purchase;
use App\Transformers\PurchaseTransformer;
use Illuminate\Http\Response;

class PurchaseController extends Controller {

    /**
     * @param \App\Http\Requests\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): Response {
        $query = Purchase::query();

        $purchaseFilter = new PurchaseFilters();
        $purchaseFilter->apply($query, $request);

        $includes = $request->getIncludes();
        $paginator = $query->with($includes)->paginate($request->getLimit());

        return response(fractal($paginator->getCollection(),
                                new PurchaseTransformer())->parseIncludes($request->getIncludes()))->withPaginationHeaders($paginator);

    }


    /**
     * Make Payment
     */
    public function create() {

    }






}
