<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\Timezone;
use App\Transformers\TimezoneTransformer;
use Illuminate\Http\JsonResponse;


class TimezoneController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $timezones = Timezone::all(['id','value','label']);

        return
            fractal($timezones, new TimezoneTransformer())
                ->parseIncludes($request->getIncludes())
                ->respond();
    }
}
