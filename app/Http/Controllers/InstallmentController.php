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
        $installments = Instalment::where('purchase_id', $purchase->id)
            ->with($request->getIncludes())
            ->orderBy('payment_date')
            ->get();

        return response(
            fractal($installments, new InstalmentTransformer())
                ->parseIncludes($request->getIncludes())
        );
    }
}
