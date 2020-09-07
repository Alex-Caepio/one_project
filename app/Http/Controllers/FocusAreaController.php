<?php

namespace App\Http\Controllers;

use App\Models\FocusArea;

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
}
