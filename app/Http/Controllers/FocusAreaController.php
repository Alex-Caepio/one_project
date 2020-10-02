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
        $focusArea = FocusArea::all();
        return fractal($focusArea, new FocusAreaTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }
    public function show(FocusArea $focusArea,Request $request)
    {
        return fractal($focusArea, new FocusAreaTransformer())
            ->parseIncludes($request->getIncludes())
            ->respond();
    }
}
