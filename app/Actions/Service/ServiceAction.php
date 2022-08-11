<?php

namespace App\Actions\Service;

use App\Models\Service;
use App\Http\Requests\Request;
use App\Traits\HasMediaItems;
use App\Traits\KeywordCollection;
use Illuminate\Support\Facades\DB;

abstract class ServiceAction
{
    use HasMediaItems, KeywordCollection;

    protected function saveService(Service $service, Request $request)
    {
        DB::transaction(function () use ($service, $request) {
            $this->fillService($service, $request);
            $this->fillRelations($service, $request);
        });
    }

    protected function fillService(Service $service, Request $request): Service
    {
        $slug = $this->getSlug($request);
        $params = [
            'title'           => $request->get('title'),
            'description'     => $request->get('description'),
            'is_published'    => $request->getBoolFromRequest('is_published'),
            'introduction'    => $request->get('introduction'),
            'slug'            => $slug,
            'image_url'       => $request->get('image_url'),
            'icon_url'        => $request->get('icon_url'),
            'service_type_id' => $request->get('service_type_id'),
        ];

        $service->forceFill($params);
        $service->save();

        return $service;
    }

    protected function fillRelations(Service $service, Request $request): void
    {
        if ($request->filled('media_images')) {
            $this->syncImages($request->media_images, $service);
        }

        if ($request->filled('media_videos')) {
            $this->syncVideos($request->media_videos, $service);
        }

        if ($request->has('media_files')) {
            $service->media_files()->delete();
            $service->media_files()->createMany($request->get('media_files'));
        }

        if ($request->filled('focus_areas')) {
            $service->focus_areas()->sync($request->get('focus_areas'));
        }

        if ($request->filled('disciplines')) {
            $service->disciplines()->sync($request->get('disciplines'));
        }

        $keywords = $this->collectKeywordModelsFromRequest($request);
        $service->keywords()->detach();

        if ($request->filled('keywords')) {
            $service->keywords()->sync($keywords);
        }
    }

    protected function getSlug($request): string
    {
        $titleSlug = $request->get('title') ?? '';

        return $request->get('slug') ?? to_url($titleSlug);
    }
}
