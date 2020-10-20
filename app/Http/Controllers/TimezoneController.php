<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\Timezone;
use App\Transformers\TimezoneTransformer;


class TimezoneController extends Controller
{
    public function index(Request $request)
    {
        $query = Timezone::query();

        $includes = $request->getIncludes();
        $paginator = $query->with($includes)
            ->paginate($request->getLimit());

        $timezone = $paginator->getCollection();

        return response(
            fractal($timezone, new TimezoneTransformer())
                ->parseIncludes($request->getIncludes())
        )->withPaginationHeaders($paginator);
    }
}

