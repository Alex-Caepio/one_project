<?php

namespace App\Http\Controllers;

use App\Actions\Article\ArticleStore;
use App\Events\ArticlePublished;
use App\Events\ArticleUnpublished;
use App\Http\Requests\Articles\ArticleRequest;
use App\Http\Requests\Request;
use App\Models\Article;
use App\Transformers\ArticleTransformer;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $paginator = Article::paginate($request->getLimit());
        $articles = $paginator->getCollection();
        return response(fractal($articles, new ArticleTransformer())
            ->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);
    }

    public function show(Article $article, Request $request)
    {
        return fractal($article, new ArticleTransformer())
        ->parseIncludes($request->getIncludes())
        ->respond();
    }

    public function store(ArticleRequest $request)
    {
        $article = run_action(ArticleStore::class,$request);
        return fractal($article, new ArticleTransformer())->respond();
    }

    public function edit(ArticleRequest $request, Article $article)
    {
        $article->update($request->all());

        return fractal($article, new ArticleTransformer())->respond();
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return response(null, 204);
    }

    public function storeFavorite(Article $article)
    {
        if ($article->articlefavorite()) {
            return response(null, 200);
        }

        Auth::user()->favourite_articles()->attach($article->id);
        return response(null, 201);
    }

    public function deleteFavorite(Article $article)
    {
        Auth::user()->favourite_articles()->detach($article->id);
        return response(null, 204);
    }
}
