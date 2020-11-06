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
        $keyword = Keyword::all();
        return fractal($keyword, new KeywordTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
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
