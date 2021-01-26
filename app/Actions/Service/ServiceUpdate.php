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
        if ($request->filled('media_images'))
        {
            foreach ($request->media_images as $mediaImage)
            {
                $image = Storage::disk(config('image.image_storage'))
                    ->put("/images/services/{$service->id}/media_images/", file_get_contents($mediaImage['url']));
                $mediaImage[] = Storage::url($image);
            }
            $request->media_images = $mediaImage;
        }
        $this->saveService($service, $request);
        return $service;
    }

}
