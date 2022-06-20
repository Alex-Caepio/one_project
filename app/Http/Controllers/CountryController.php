<?php

namespace App\Http\Controllers;

use App\Filters\CountryFiltrator;
use App\Http\Requests\Request;
use App\Models\Country;
use App\Transformers\CountryTransformer;

class CountryController extends Controller {

    public function index(Request $request) {
        $countryQuery = Country::query();
        $countryFilter = new CountryFiltrator();
        $countryFilter->apply($countryQuery, $request);
        return fractal($countryQuery->get(), new CountryTransformer())->respond();

    }
}
