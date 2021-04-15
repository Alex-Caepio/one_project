<?php

namespace App\Http\Controllers;

use App\Actions\Article\ArticleStore;
use App\Actions\Article\ArticleUpdate;
use App\Filters\ArticleFiltrator;
use App\Filters\ServiceFiltrator;
use App\Http\Requests\Admin\ArticlePublishRequest;
use App\Http\Requests\Articles\ArticleActionRequest;
use App\Http\Requests\Articles\ArticleRequest;
use App\Http\Requests\Request;
use App\Models\Article;
use App\Transformers\ArticleTransformer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{

    /**
     * @param \App\Http\Requests\Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $paginator = $this->getArticleList($request);
        $articles = $paginator->getCollection();

        return response(fractal($articles, new ArticleTransformer())->parseIncludes($request->getIncludes())->toArray())
            ->withPaginationHeaders($paginator);
    }

    /**
     * @param \App\Models\Article        $publicArticle
     * @param \App\Http\Requests\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Article $publicArticle, Request $request)
    {
        return fractal($publicArticle, new ArticleTransformer())->parseIncludes($request->getIncludes())->respond();
    }

    /**
     * @param \App\Http\Requests\Articles\ArticleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ArticleRequest $request)
    {
        $article = run_action(ArticleStore::class, $request);
        return fractal($article, new ArticleTransformer())->respond();
    }

    /**
     * @param \App\Http\Requests\Articles\ArticleRequest $request
     * @param \App\Models\Article                        $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ArticleRequest $request, Article $article)
    {
        $article = run_action(ArticleUpdate::class, $request, $article);
        return fractal($article, new ArticleTransformer())->respond();
    }

    /**
     * @param \App\Models\Article $article
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function storeFavorite(Article $article)
    {
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
    public function deleteFavorite(Article $article)
    {
        Auth::user()->favourite_articles()->detach($article->id);
        return response(null, 204);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function practitionerArticleList(Request $request)
    {
        $paginator = $this->getArticleList($request, Auth::user()->id, false);
        return response(fractal($paginator->getCollection(),
            new ArticleTransformer())->parseIncludes($request->getIncludes()))->withPaginationHeaders($paginator);
    }


    public function practitionerArticleShow(Article $article, ArticleActionRequest $request)
    {
        return fractal($article, new ArticleTransformer())->parseIncludes($request->getIncludes())->respond();
    }

    /**
     * @param ArticleActionRequest $request
     * @param \App\Models\Article  $article
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(ArticleActionRequest $request, Article $article)
    {
        $article->delete();
        return response(null, 204);
    }

    /**
     * @param \App\Http\Requests\Request $request
     * @param int|null                   $userId
     * @param bool                       $isPublished
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getArticleList(Request $request, ?int $userId = null,
                                    bool $isPublished = true): LengthAwarePaginator
    {
        $queryBuilder = Article::with($request->getIncludes());
        $articleFiltrator = new ArticleFiltrator();
        $articleFiltrator->apply($queryBuilder, $request);

        if ($userId !== null) {
            $queryBuilder->where('user_id', $userId);
        }

        if ($request->has('recently_published')) {
            $queryBuilder->orderByDesc('published_at');
        }

        if ($isPublished) {
            $queryBuilder->published()->whereHas('user', function ($query) {
                $query->published();
            });
        }

        return $queryBuilder->paginate($request->getLimit());
    }

    /**
     * @param \App\Models\Article                            $article
     * @param \App\Http\Requests\Admin\ArticlePublishRequest $publishRequest
     * @return \Illuminate\Http\Response
     */
    public function publish(Article $article, ArticlePublishRequest $publishRequest): Response
    {
        $article->forceFill([
            'is_published' => true,
            'published_at' => now()
        ]);
        $article->save();
        return response(null, 204);
    }


    /**
     * @param \App\Models\Article $article
     * @return \Illuminate\Http\Response
     */
    public function unpublish(Article $article): Response
    {
        $article->forceFill(['is_published' => false]);
        $article->save();
        return response(null, 204);
    }

    public function copy(Article $article) {
        $newArticle = $article->replicate();
        $newArticle->title = "{$article->title} (copy)";
        $newArticle->is_published = false;
        $newArticle->save();

        return fractal($newArticle, new ArticleTransformer())->respond();
    }

}
