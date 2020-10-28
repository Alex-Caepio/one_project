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
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller {

    public function index(Request $request) {
        $paginator = Article::published()->whereHas('user', function($query) {
            $query->published();
        })->paginate($request->getLimit());
        $articles = $paginator->getCollection();
        return response(fractal($articles, new ArticleTransformer())->parseIncludes($request->getIncludes()))->withPaginationHeaders($paginator);
    }

    public function show(Article $article, Request $request) {
        return fractal($article, new ArticleTransformer())->parseIncludes($request->getIncludes())->respond();
    }

    public function store(ArticleRequest $request) {
        $article = $this->saveArticle(new Article(), $request);
        return fractal($article, new ArticleTransformer())->respond();
    }

    public function edit(ArticleRequest $request, Article $article) {
        return fractal($this->saveArticle($article, $request), new ArticleTransformer())->respond();
    }

    public function destroy(Article $article) {
        $article->delete();
        return response(null, 204);
    }

    public function storeFavorite(Article $article) {
        if ($article->articlefavorite()) {
            return response(null, 200);
        }

        Auth::user()->favourite_articles()->attach($article->id);
        return response(null, 201);
    }

    public function deleteFavorite(Article $article) {
        Auth::user()->favourite_articles()->detach($article->id);
        return response(null, 204);
    }


    /**
     * @param \App\Models\Article $article
     * @param \App\Http\Requests\Articles\ArticleRequest $request
     * @return \App\Models\Article
     */
    private function saveArticle(Article $article, ArticleRequest $request): Article {
        $requestData = $request->toArray();
        $requestData['user_id'] = Auth::id();
        $article->forceFill($requestData);

        if ($article->save()) {
            if ($request->has('media_images')) {
                if (!$article->wasRecentlyCreated) {
                    $article->media_images()->delete();
                }
                $article->media_images()->createMany($request->get('media_images'));
            }
            if ($request->has('media_videos')) {
                if (!$article->wasRecentlyCreated) {
                    $article->media_videos()->delete();
                }
                $article->media_videos()->createMany($request->get('media_videos'));
            }
            if ($request->has('media_files')) {
                if (!$article->wasRecentlyCreated) {
                    $article->media_files()->delete();
                }
                $article->media_files()->createMany($request->get('media_files'));
            }

            if ($article->wasRecentlyCreated) {
                $user = Auth::user();
                try {
                    if (!$article) {
                        event(new ArticleUnpublished($article, $user));
                    } else {
                        event(new ArticlePublished($article, $user));
                    }
                } catch (\Exception $e) {
                    Log::info($e->getMessage());
                }
            }

        }
        return $article;
    }


}
