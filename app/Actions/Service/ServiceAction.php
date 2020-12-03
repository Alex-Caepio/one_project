<?php

namespace App\Actions\Service;

use App\Http\Requests\Request;
use App\Http\Requests\Services\StoreServiceRequest;
use App\Models\Keyword;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

abstract class ServiceAction {

    /**
     * @param \App\Models\Service $service
     * @param \App\Http\Requests\Services\StoreServiceRequest $request
     */
    protected function saveService(Service $service, StoreServiceRequest $request) {
        DB::transaction(function() use ($service, $request) {
            $this->fillService($service, $request);
            $this->fillRelations($service, $request);
        });
    }


    /**
     * @param \App\Models\Service $service
     * @param \App\Http\Requests\Services\StoreServiceRequest $request
     * @return \App\Models\Service
     */
    protected function fillService(Service $service, StoreServiceRequest $request): Service {
        $url = $request->get('url') ?? to_url($request->get('title'));
        $params = [
            'title'           => $request->get('title'),
            'description'     => $request->get('description'),
            'is_published'    => $request->getBoolFromRequest('is_published'),
            'introduction'    => $request->get('introduction'),
            'url'             => $url,
            'image_url'       => $request->get('image_url'),
            'icon_url'        => $request->get('icon_url'),
            'service_type_id' => $request->get('service_type_id'),
            'user_id'         => Auth::id(),
        ];
        $service->forceFill($params);
        $service->save();
        return $service;
    }

    /**
     * @param \App\Models\Service $service
     * @param \App\Http\Requests\Services\StoreServiceRequest $request
     */
    protected function fillRelations(Service $service, StoreServiceRequest $request): void {
        if ($request->has('media_images')) {
            $service->media_images()->delete();
            $service->media_images()->createMany($request->get('media_images'));
        }

        if ($request->has('media_videos')) {
            $service->media_videos()->delete();
            $service->media_videos()->createMany($request->get('media_videos'));
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
        if (count($keywords)) {
            $service->keywords()->sync($keywords);
        }

    }

    /**
     * @param \App\Http\Requests\Request $request
     * @return array
     */
    private function collectKeywordModelsFromRequest(Request $request): array {
        $ids = [];
        if ($request->filled('keywords') && is_array($request->get('keywords'))) {
            $keywords = array_unique($request->get('keywords'));
            foreach ($keywords as $keyword) {
                $keyword = Keyword::firstOrCreate(['title' => strtoupper($keyword)]);
                $ids[] = $keyword->id;
            }
        }
        return $ids;
    }

}
