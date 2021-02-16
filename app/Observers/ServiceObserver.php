<?php

namespace App\Observers;

use App\Events\ServiceListingLive;
use App\Events\ServiceUnpublished;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServiceObserver {

    /**
     * Handle the article "updated" event.
     *
     * @param \App\Models\Service $service
     * @return void
     */
    public function saved(Service $service) {
        if ($service->isDirty('is_published')) {
            if (!$service->is_published && !$service->wasRecentlyCreated) {
                event(new ServiceUnpublished($service, Auth::user()));
            } elseif ($service->is_published) {
                event(new ServiceListingLive($service, Auth::user()));
            }
        }
    }


}
