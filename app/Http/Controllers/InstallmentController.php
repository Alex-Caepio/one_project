<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\Instalment;
use App\Models\Purchase;
use App\Transformers\InstalmentTransformer;

class InstallmentController extends Controller
{
    public function getInstallments(Purchase $purchase, Request $request)
    {
        $installments = Instalment::where('purchase_id', $purchase)->with($request->getIncludes())->get();
        return response(
            fractal(
                $installments,
                new InstalmentTransformer()
            )->parseIncludes($request->getIncludes())
        );
    }
}
