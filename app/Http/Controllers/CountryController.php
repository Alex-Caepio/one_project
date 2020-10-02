<?php

namespace App\Http\Controllers;

use App\Actions\Country\CountryFilter;
use App\Http\Requests\Request;
use App\Transformers\CountryTransformer;

class CountryController extends Controller
{
    public function filter(Request $request){
        $country = run_action(CountryFilter::class, $request);
        return fractal($country, new CountryTransformer())->respond();
    }
}
