<?php


namespace App\Actions\Service;

use App\Http\Requests\Services\StoreServiceRequest;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;

class ServiceUpdate extends ServiceAction {

    /**
     * @param \App\Http\Requests\Services\StoreServiceRequest $request
     * @param \App\Models\Service $service
     * @return \App\Models\Service
     */
    public function execute(StoreServiceRequest $request, Service $service): Service {
//        if ($request->filled('media_images') && !empty($request->media_images))
//        {
//            foreach ($request->media_images as $mediaImage)
//            {
//                if (Storage::disk(config('image.image_storage'))->missing(file_get_contents($mediaImage['url'])))
//                {
//                    $image = Storage::disk(config('image.image_storage'))
//                        ->put("/images/services/{$service->id}/media_images/", file_get_contents($mediaImage['url']));
//                    $image_urls[]['url'] = Storage::url($image);
//                }
//            }
//            $request->media_images = $image_urls;
//        }
        $this->saveService($service, $request);

        if ($request->filled('media_images') && !empty($request->media_images)){
            $service->media_images()->whereNotIn('url', $request->media_images)->delete();
            $urls = collect($request->media_images)->pluck('url');
            $recurringURL = $service->media_images()->whereIn('url', $urls)->pluck('url')->toArray();
            $newImages = $urls->filter(function($value) use ($recurringURL) {
                return !in_array($value, $recurringURL);
            });

            $imageUrlToStore = [];
            foreach ($newImages as $url) {
                $imageUrlToStore[]['url'] = $url;
            }

            if ($imageUrlToStore) {
                $service->media_images()->createMany($imageUrlToStore);
            }
        }
        if ($request->filled('media_videos') && !empty($request->media_videos)) {
            $service->media_videos()->whereNotIn('url', $request->media_videos)->delete();
            $urls = collect($request->media_videos)->pluck('url');
            $recurringURL = $service->media_videos()->whereIn('url', $urls)->pluck('url')->toArray();
            $newVideos = $urls->filter(function($value) use ($recurringURL) {
                return !in_array($value, $recurringURL);
            });

            $videoUrlToStore = [];
            foreach ($newVideos as $url) {
                $videoUrlToStore[]['url'] = $url;
            }

            if ($videoUrlToStore) {
                $service->media_videos()->createMany($videoUrlToStore);
            }
        }

        return $service;
    }

}
