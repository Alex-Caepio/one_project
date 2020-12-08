<?php

namespace App\Http\Controllers;

use App\Models\MainPage;
use App\Http\Requests\Request;
use App\Transformers\MainPageTransformer;

class MainPageController extends Controller
{
    public function index(Request $request)
    {
        $includes = $request->getIncludes();

        return fractal(MainPage::with($includes)->first(), new MainPageTransformer())
            ->parseIncludes($includes)
            ->respond();
    }

}
