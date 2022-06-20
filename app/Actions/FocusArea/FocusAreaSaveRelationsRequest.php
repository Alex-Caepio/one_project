<?php


namespace App\Actions\FocusArea;


use App\Http\Requests\Request;
use App\Models\FocusArea;
use App\Traits\hasMediaItems;

class FocusAreaSaveRelationsRequest
{
    use hasMediaItems;

    public function execute(FocusArea $focusArea, Request $request): void
    {
        if ($request->filled('featured_practitioners')) {
            $focusArea->featured_practitioners()->sync($request->get('featured_practitioners'));
        }
        if ($request->filled('featured_disciplines')) {
            $focusArea->featured_disciplines()->sync($request->get('featured_disciplines'));
        }
        if ($request->filled('featured_articles')) {
            $focusArea->featured_articles()->sync($request->get('featured_articles'));
        }
        if ($request->filled('featured_services')) {
            $focusArea->featured_services()->sync($request->get('featured_services'));
        }
        if ($request->filled('featured_focus_areas')) {
            $focusArea->featured_focus_areas()->sync($request->get('featured_focus_areas'));
        }

        if ($request->filled('media_images')) {
            $this->syncImages($request->media_images, $focusArea);
        }
        if ($request->filled('media_videos')) {
            $this->syncVideos($request->media_videos, $focusArea);
        }
        if ($request->filled('media_files')) {
            $focusArea->media_files()->createMany($request->get('media_files'));
        }

        if ($request->filled('practitioners')) {
            $focusArea->practitioners()->sync($request->get('users'));
        }
        if ($request->filled('services')) {
            $focusArea->services()->sync($request->get('services'));
        }
        if ($request->filled('articles')) {
            $focusArea->articles()->sync($request->get('articles'));
        }
        if ($request->filled('disciplines')) {
            $focusArea->disciplines()->sync($request->get('disciplines'));
        }
    }
}
