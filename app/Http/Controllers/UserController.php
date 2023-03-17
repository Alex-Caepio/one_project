<?php

namespace App\Http\Controllers;

use App\Actions\Test\GetUser;
use App\Filters\UserFiltrator;
use App\Http\Requests\Request;
use App\Models\User;
use App\Transformers\UserTransformer;
use App\Transformers\ArticleTransformer;
use App\Transformers\ServiceTransformer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller {

    public function show(Request $request, User $user)
    {
        $query = User::with($request->getIncludes())->find($user->id);
        return fractal($query, new UserTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function serviceFavorites(Request $request) {
        $serviceFavorites = Auth::user()->favourite_services;
        return fractal($serviceFavorites, new ServiceTransformer())
            ->parseIncludes($request->getIncludes())->toArray();
    }

    public function articleFavorites(Request $request) {
        $articleFavorites = Auth::user()->favourite_articles;
        return fractal($articleFavorites, new ArticleTransformer())
            ->parseIncludes($request->getIncludes())->toArray();
    }

    public function practitionerFavorites(Request $request) {
        $practitionerFavorites = Auth::user()->favourite_practitioners;
        return fractal($practitionerFavorites, new UserTransformer())
            ->parseIncludes($request->getIncludes())->toArray();
    }


    /**
     * @param \App\Http\Requests\Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request): Response {
        $query = User::query();

        $userFilter = new UserFiltrator();
        $userFilter->apply($query, $request);

        $includes = $request->getIncludes();
        $paginator = $query->with($includes)->paginate($request->getLimit());

        $users = $paginator->getCollection();

        return response(fractal($users, new UserTransformer())->parseIncludes($includes)
                                                              ->toArray())->withPaginationHeaders($paginator);
    }
}
