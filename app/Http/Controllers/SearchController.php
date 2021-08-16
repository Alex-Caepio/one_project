<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\Article;
use App\Models\Service;
use App\Models\User;
use App\Transformers\ArticleTransformer;
use App\Transformers\ServiceTransformer;
use App\Transformers\UserTransformer;


class SearchController extends Controller {

    public function index(Request $request) {
        $articles = Article::query()-published();
        $services = Service::query()-published();
        $practitioners = User::query()->where('account_type', 'practitioner')-published();
        $search = $request['q'];

        $articles->where(
                function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%");
                }
            )->orderBy('articles.id', 'desc');

        $services->where(
            function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            }
        )->orderBy('services.id', 'desc');

        $practitioners->where(
            function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('business_city', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%");
            }
        )->orderBy('id', 'desc');

        return [
            'articles' => fractal($articles->limit(10)->get(), new ArticleTransformer())->toArray(),
            'practitioners' => fractal($practitioners->limit(10)->get(), new UserTransformer())->toArray(),
            'services' => fractal($services->limit(10)->get(), new ServiceTransformer())->toArray(),
        ];
    }
}
