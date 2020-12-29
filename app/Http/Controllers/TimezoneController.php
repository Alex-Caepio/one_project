<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\Timezone;
use App\Transformers\TimezoneTransformer;


class TimezoneController extends Controller
{
    public function index(Request $request)
    {
        $timezones = Timezone::with($request->getIncludes())->get();

        return
            fractal($timezones, new TimezoneTransformer())
                ->parseIncludes($request->getIncludes())
                ->respond();
    }
}

