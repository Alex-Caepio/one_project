<?php

namespace App\Http\Controllers;

use App\Models\Discipline;
use App\Http\Requests\Request;
use App\Transformers\DisciplineTransformer;

class DisciplineController extends Controller
{
    public function index(Request $request)
    {
        $discipline = Discipline::where('is_published', true)->get();
        return fractal($discipline, new DisciplineTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function show(Discipline $discipline, Request $request)
    {
        return fractal($discipline, new DisciplineTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }
}
