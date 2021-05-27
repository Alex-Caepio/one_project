<?php

namespace App\Observers;

use App\Events\ServiceListingLive;
use App\Events\ServiceUnpublished;
use App\Models\Service;
use Carbon\Carbon;
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
                event(new ServiceUnpublished($service));
            } elseif ($service->is_published) {
                event(new ServiceListingLive($service));
            }
        }
    }

    public function saving(Service $service) {
        if ($service->isDirty('is_published')) {
            if (!$service->is_published) {
                $this->clearPublishedState($service);
            } else {
                $publishedDate = Carbon::now()->format('Y-m-d H:i:s');
                $service->last_published = $publishedDate;
                if (!$service->getOriginal('published_at')) {
                    $service->published_at = $publishedDate;
                }
            }
        }
    }

    /**
     * Handle the article "deleting" event.
     * Drop Published Fields.
     *
     * @param \App\Models\Service $service
     * @return void
     */
    public function deleting(Service $service) {
        $this->clearPublishedState($service);
        $service->saveQuietly();
    }


    /**
     * @param \App\Models\Service $service
     */
    private function clearPublishedState(Service $service): void {
        $service->forceFill(['is_published' => false]);
    }

}
