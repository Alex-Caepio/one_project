<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\Timezone;
use App\Transformers\TimezoneTransformer;


class TimezoneController extends Controller
{
    public function index(Request $request)
    {
        $timezones = Timezone::with($request->getIncludes())->all();

        return
            fractal($timezones, new TimezoneTransformer())
                ->parseIncludes($request->getIncludes())
                ->respond();
    }
}

