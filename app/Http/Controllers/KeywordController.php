<?php

namespace App\Http\Controllers;

use App\Actions\Keyword\KeywordFilter;
use App\Http\Requests\Request;
use App\Models\Keyword;
use App\Transformers\KeywordTransformer;

class KeywordController extends Controller
{

    public function index(Request $request)
    {
        $discipline = Keyword::all();
        return fractal($discipline, new KeywordTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function list()
    {
        $discipline = Keyword::query();
        return $discipline->pluck('title', 'id');
    }

    public function filter(Request $request)
    {
        $keyword = run_action(KeywordFilter::class, $request);
        return fractal($keyword, new KeywordTransformer())->respond();
    }
}
