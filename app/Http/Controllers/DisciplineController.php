<?php

namespace App\Http\Controllers;

use App\Models\Discipline;
use App\Http\Requests\Request;
use App\Transformers\DisciplineTransformer;

class DisciplineController extends Controller {
    public function index(Request $request)
    {
        $query = Discipline::query()->where('is_published', true);

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $query->orderBy($order['column'], $order['direction']);
        }

        if ($request->hasSearch()) {
            $search = $request->search();

            $query->where(
                function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('introduction', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                }
            );
        }

        $includes  = $request->getIncludes();
        $paginator = $query->with($includes)
            ->paginate($request->getLimit());

        $discipline = $paginator->getCollection();

        return response(fractal($discipline, new DisciplineTransformer())
            ->parseIncludes($includes)->toArray())
            ->withPaginationHeaders($paginator);
    }

    public function show(Discipline $discipline, Request $request)
    {
        $discipline->load($request->getIncludesOnlyPublished());
        return fractal($discipline, new DisciplineTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }
}
