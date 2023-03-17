<?php

namespace App\Actions\Discipline;

use App\Http\Requests\Request;
use App\Models\Discipline;
use App\Traits\HasMediaItems;

class DisciplineSaveRelationsRequest
{
    use HasMediaItems;

    public function execute(Discipline $discipline, Request $request): void
    {
        if ($request->filled('featured_practitioners')) {
            $discipline->featured_practitioners()->sync($request->get('featured_practitioners'));
        }
        if ($request->filled('featured_services')) {
            $discipline->featured_services()->sync($request->get('featured_services'));
        }
        if ($request->filled('focus_areas')) {
            $discipline->focus_areas()->sync($request->get('focus_areas'));
        }
        if ($request->filled('related_disciplines')) {
            $discipline->related_disciplines()->sync($request->get('related_disciplines'));
        }
        if ($request->filled('featured_at_focus_area')) {
            $discipline->featured_focus_area()->sync($request->get('featured_focus_area'));
        }
        if ($request->filled('featured_articles')) {
            $discipline->featured_articles()->sync($request->get('featured_articles'));
        }
        if ($request->filled('featured_focus_areas')) {
            $discipline->featured_focus_areas()->sync($request->get('featured_focus_areas'));
        }
        if ($request->filled('media_images')) {

            $this->syncImages($request->media_images, $discipline);
        }
        if ($request->filled('media_videos')) {
            $this->syncVideos($request->media_videos, $discipline);
        }
        if ($request->has('media_files')) {
            $discipline->media_files()->delete();
            $discipline->media_files()->createMany($request->get('media_files'));
        }
    }
}
