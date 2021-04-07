<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\FocusArea;
use App\Transformers\FocusAreaTransformer;

class FocusAreaController extends Controller
{
    public function indexImage(FocusArea $focusArea)
    {
        $allImage = $focusArea->focus_area_images;
        return response($allImage);
    }
    public function indexVideo(FocusArea $focusArea)
    {
        $allVideos = $focusArea->focus_area_videos;
        return response($allVideos);
    }

    public function index(Request $request)
    {
        $query = FocusArea::query()->where('is_published', true);

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

        $focus = $paginator->getCollection();

        return response(fractal($focus, new FocusAreaTransformer())
            ->parseIncludes($includes)->toArray())
            ->withPaginationHeaders($paginator);
    }
    public function show(FocusArea $focusArea,Request $request)
    {
        return fractal($focusArea, new FocusAreaTransformer())
            ->parseIncludes($request->getIncludes())
            ->respond();
    }
}
