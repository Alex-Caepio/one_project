<?php

namespace App\Http\Controllers\Admin;


use App\Filters\ArticleFiltrator;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Http\Requests\Request;
use App\Transformers\ArticleTransformer;
use Illuminate\Http\Response;

class ArticleController extends Controller {

    public function index(Request $request): Response {
        $query = Article::query();

        $articleFilter = new ArticleFiltrator();
        $articleFilter->apply($query, $request);

        $includes = $request->getIncludes();
        $paginator = $query->with($includes)->paginate($request->getLimit());

        $article = $paginator->getCollection();

        return response(fractal($article, new ArticleTransformer())->parseIncludes($includes)->toArray())->withPaginationHeaders($paginator);
    }

}
