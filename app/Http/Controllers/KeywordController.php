<?php

namespace App\Http\Controllers;

use App\Actions\Keyword\KeywordFilter;
use App\Http\Requests\Request;
use App\Models\Keyword;
use App\Transformers\KeywordTransformer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class KeywordController extends Controller
{

    public function index(Request $request)
    {
        $query = Keyword::query();

        if ($request->hasSearch()) {
            $search = $request->search();

            $query->where(
                function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%");
                }
            );
        }

        $includes  = $request->getIncludes();
        $paginator = $query->with($includes)
            ->paginate($request->getLimit());

        $keyword = $paginator->getCollection();

        return response(fractal($keyword, new KeywordTransformer())
            ->parseIncludes($includes)->toArray())
            ->withPaginationHeaders($paginator);
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function list(): Collection {
        $keyword = Keyword::query();
        return $keyword->pluck('title', 'id');
    }

    public function filter(Request $request)
    {
        $keyword = run_action(KeywordFilter::class, $request);
        return fractal($keyword, new KeywordTransformer())->respond();
    }
}
