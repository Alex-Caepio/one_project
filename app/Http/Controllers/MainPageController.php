<?php

namespace App\Http\Controllers;

use App\Models\MainPage;
use App\Http\Requests\Request;
use App\Transformers\MainPageTransformer;

class MainPageController extends Controller {
    
    public function index(Request $request) {
        return fractal(MainPage::with($request->getIncludesOnlyPublished())->first(), new MainPageTransformer())
            ->parseIncludes($request->getIncludes())->respond();
    }

}
