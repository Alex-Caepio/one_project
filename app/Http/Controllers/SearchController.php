<?php

namespace App\Http\Controllers;

use App\Filters\ArticleFiltrator;
use App\Filters\ServiceFiltrator;
use App\Filters\UserFiltrator;
use App\Http\Requests\Request;
use App\Models\Article;
use App\Models\Service;
use App\Models\User;
use App\Transformers\ArticleTransformer;
use App\Transformers\ServiceTransformer;
use App\Transformers\UserTransformer;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::query()->where('articles.is_published', true)->orderBy('articles.id', 'desc');
        $services = Service::query()->where('services.is_published', true)->orderBy('services.id', 'desc');
        $practitioners = User::where('account_type', User::ACCOUNT_PRACTITIONER)->where('users.is_published', true)->orderBy('id', 'desc');

        $articleFiltrator = new ArticleFiltrator();
        $articleFiltrator->apply($articles, $request, true);

        $serviceFiltrator = new ServiceFiltrator();
        $serviceFiltrator->apply($services, $request);

        $userFiltrator = new UserFiltrator();
        $userFiltrator->apply($practitioners, $request, true);

        return [
            'articles' => fractal($articles->limit(10)->get(), new ArticleTransformer())->toArray(),
            'practitioners' => fractal($practitioners->limit(10)->get(), new UserTransformer())->toArray(),
            'services' => fractal($services->limit(10)->get(), new ServiceTransformer())->toArray(),
        ];
    }
}
