<?php

namespace App\Http\Controllers;

use App\Actions\Article\ArticleStore;
use App\Actions\Article\ArticleUpdate;
use App\Http\Requests\Articles\ArticleDeleteRequest;
use App\Http\Requests\Articles\ArticleRequest;
use App\Http\Requests\Articles\PractitionerArticleRequest;
use App\Http\Requests\Request;
use App\Models\Article;
use App\Transformers\ArticleTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller {

    /**
     * @param \App\Http\Requests\Request $request
     * @return mixed
     */
    public function index(Request $request) {
        $includes = $request->getIncludes();
        $paginator = Article::published()->whereHas('user', function($query) {
            $query->published();
        })->with($includes)->paginate($request->getLimit());
        $articles = $paginator->getCollection();

        return response(fractal($articles, new ArticleTransformer())->parseIncludes($includes))->withPaginationHeaders($paginator);
    }

    /**
     * @param \App\Models\Article $publicArticle
     * @param \App\Http\Requests\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Article $publicArticle, Request $request) {
        return fractal($publicArticle, new ArticleTransformer())->parseIncludes($request->getIncludes())->respond();
    }

    /**
     * @param \App\Http\Requests\Articles\ArticleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ArticleRequest $request) {
        $article = run_action(ArticleStore::class, $request);
        return fractal($article, new ArticleTransformer())->respond();
    }

    /**
     * @param \App\Http\Requests\Articles\ArticleRequest $request
     * @param \App\Models\Article $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(ArticleRequest $request, Article $article) {
        $article = run_action(ArticleUpdate::class, $request, $article);
        return fractal($article, new ArticleTransformer())->respond();
    }

    /**
     * @param \App\Http\Requests\Articles\ArticleDeleteRequest $request
     * @param \App\Models\Article $article
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(ArticleDeleteRequest $request, Article $article) {
        $article->delete();
        return response(null, 204);
    }

    /**
     * @param \App\Models\Article $article
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function storeFavorite(Article $article) {
        if ($article->articlefavorite()) {
            return response(null, 200);
        }

        Auth::user()->favourite_articles()->attach($article->id);
        return response(null, 201);
    }

    /**
     * @param \App\Models\Article $article
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function deleteFavorite(Article $article) {
        Auth::user()->favourite_articles()->detach($article->id);
        return response(null, 204);
    }



    public function practitionerList(PractitionerArticleRequest $request) {

    }

    public function practitionerShow(PractitionerArticleRequest $request) {

    }

}
