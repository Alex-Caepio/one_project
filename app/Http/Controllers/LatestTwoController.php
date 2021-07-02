<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Service;
use App\Models\User;
use App\Transformers\ArticleTransformer;
use App\Transformers\ServiceTransformer;
use App\Transformers\UserTransformer;


class LatestTwoController extends Controller {

    public function index() {
        $articles = Article::where('is_published', true)
            ->limit(2)->orderBy('published_at', 'desc')
            ->get();

        $services = Service::where('is_published', true)
            ->with(['active_schedules'])
            ->limit(2)->orderBy('published_at', 'desc')
            ->get();

        $practitioners = User::where('account_type', 'practitioner')
            ->where('is_published', true)
            ->limit(2)->orderBy('published_at', 'desc')
            ->get();

        return [
            'articles' => fractal($articles, new ArticleTransformer())->toArray(),
            'practitioners' => fractal($practitioners, new UserTransformer())->toArray(),
            'services' => fractal($services, new ServiceTransformer())->parseIncludes(['active_schedules'])->toArray(),
        ];
    }
}
