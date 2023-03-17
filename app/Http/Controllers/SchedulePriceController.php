<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\Price;
use App\Models\Schedule;
use App\Transformers\PriceTransformer;

class SchedulePriceController extends Controller
{
    public function store(Request $request, Schedule $schedule)
    {
        $data               = $request->all();
        $data['schedule_id'] = $schedule->id;
        $price           = Price::create($data);

        return fractal($price, new PriceTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }
}
