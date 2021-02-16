<?php

namespace App\Http\Controllers\Admin;


use App\Filters\ArticleFiltrator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticlePublishRequest;
use App\Models\Article;
use App\Http\Requests\Request;
use App\Transformers\ArticleTransformer;
use Illuminate\Http\Response;

class ArticleController extends Controller {


    /**
     * @param \App\Http\Requests\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): Response {
        $query = Article::query();

        $articleFilter = new ArticleFiltrator();
        $articleFilter->apply($query, $request);

        $paginator = $query->with($request->getIncludesWithTrashed(['user']))
                           ->paginate($request->getLimit());

        $article = $paginator->getCollection();

        return response(fractal($article, new ArticleTransformer())
                            ->parseIncludes($request->getIncludes())
                            ->toArray())->withPaginationHeaders($paginator);
    }


    /**
     * @param \App\Models\Article $article
     * @param \App\Http\Requests\Admin\ArticlePublishRequest $publishRequest
     * @return \Illuminate\Http\Response
     */
    public function publish(Article $article, ArticlePublishRequest $publishRequest): Response {
        $article->forceFill(['is_published' => true]);
        $article->save();
        return response(null, 204);
    }


    /**
     * @param \App\Models\Article $article
     * @return \Illuminate\Http\Response
     */
    public function unpublish(Article $article): Response {
        $article->forceFill(['is_published' => false]);
        $article->save();
        return response(null, 204);
    }


    /**
     * @param \App\Models\Article $article
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Article $article): Response {
        $article->delete();
        return response(null, 204);
    }


}
