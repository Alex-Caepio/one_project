<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\Article;
use App\Models\Service;
use App\Models\User;
use App\Transformers\ArticleTransformer;
use App\Transformers\ServiceTransformer;
use App\Transformers\UserTransformer;

class LatestTwoController extends Controller
{
    public function index(Request $request)
    {
        $includes = $request->getIncludes();

        $articles = Article::published()->whereHas(
            'user',
            static function ($userQuery) {
                $userQuery->where('is_published', true);
            }
        )->with($includes)->limit(2)->orderBy('last_published', 'desc')->get();

        $servicesIncludes = array_merge(['active_schedules'], $includes);
        $services = Service::published()->whereHas(
            'user',
            static function ($userQuery) {
                $userQuery->where('is_published', true);
            }
        )->with($servicesIncludes)->limit(2)->orderBy('last_published', 'desc')->get();

        $practitioners = User::where('account_type', User::ACCOUNT_PRACTITIONER)->where('is_published', true)->limit(2)->orderBy(
            'business_published_at',
            'desc'
        )->get();

        return [
            'articles' => fractal($articles, new ArticleTransformer())->parseIncludes($includes)->toArray(),
            'practitioners' => fractal($practitioners, new UserTransformer())->toArray(),
            'services' => fractal($services, new ServiceTransformer())->parseIncludes($servicesIncludes)->toArray(),
        ];
    }
}
