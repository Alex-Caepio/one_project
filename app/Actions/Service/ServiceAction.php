<?php

namespace App\Actions\Service;

use App\Models\Keyword;
use App\Models\Service;
use App\Http\Requests\Request;
use App\Traits\hasMediaItems;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

abstract class ServiceAction {

    use hasMediaItems;
    /**
     * @param \App\Models\Service $service
     * @param \App\Http\Requests\Request $request
     */
    protected function saveService(Service $service, Request $request) {
        DB::transaction(function() use ($service, $request) {
            $this->fillService($service, $request);
            $this->fillRelations($service, $request);
        });
    }


    /**
     * @param \App\Models\Service $service
     * @param \App\Http\Requests\Request $request
     * @return \App\Models\Service
     */
    protected function fillService(Service $service, Request $request): Service {
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
     * @param \App\Http\Requests\Request $request
     */
    protected function fillRelations(Service $service, Request $request): void {
        if ($request->filled('media_images'))
        {
//            foreach ($request->media_images as $mediaImage)
//            {
//                if (Storage::disk(config('image.image_storage'))->missing(file_get_contents($mediaImage)))
//                {
//                    $image = Storage::disk(config('image.image_storage'))
//                        ->put("/images/services/{$service->id}/media_images/", file_get_contents($mediaImage));
//                    $image_urls[] = Storage::url($image);
//                }
//            }
//            $request->media_images = $image_urls;
            $this->syncImages($request->media_images,$service);
        }


        if ($request->filled('media_videos')) {
            $this->syncVideos($request->media_videos,$service);
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
        if ($request->filled('keywords')) {
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
