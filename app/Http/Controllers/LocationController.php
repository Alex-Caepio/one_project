<?php

namespace App\Http\Controllers;

use App\Actions\Location\LocationFilter;
use App\Actions\Location\LocationList;
use App\Http\Requests\Request;
use App\Transformers\LocationTransformer;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $location = run_action(LocationFilter::class, $request);
        return fractal($location, new LocationTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function list(Request $request)
    {
        $location = run_action(LocationList::class, $request);
        return response($location);
    }

}
