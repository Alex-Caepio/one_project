<?php

namespace App\Http\Controllers;

use App\Actions\Test\GetUser;
use App\Http\Requests\Request;
use App\Transformers\UserTransformer;
use App\Transformers\ArticleTransformer;
use App\Transformers\ServiceTransformer;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function serviceFavorites(Request $request)
    {
        $serviceFavorites = Auth::user()->favourite_services;
        return fractal($serviceFavorites, new ServiceTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function articleFavorites(Request $request)
    {
        $articleFavorites = Auth::user()->favourite_articles;
        return fractal($articleFavorites, new ArticleTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function practitionerFavorites(Request $request)
    {
        $practitionerFavorites = Auth::user()->favourite_practitioners;
        return fractal($practitionerFavorites, new UserTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

}
