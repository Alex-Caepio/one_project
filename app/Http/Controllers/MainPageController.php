<?php

namespace App\Http\Controllers;

use App\Models\MainPage;
use App\Transformers\MainPageTransformer;


class MainPageController extends Controller
{
    public function index()
    {
        return fractal(MainPage::first(), new MainPageTransformer())->respond();
    }

}
