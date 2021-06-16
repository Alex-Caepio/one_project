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
                $this->unpublishSchedules($service);
                event(new ServiceUnpublished($service));
            } elseif ($service->is_published) {
                event(new ServiceListingLive($service));
            }
        }
    }

    /**
     * @param \App\Models\Service $service
     */
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
    public function deleting(Service $service): void {
        $this->clearPublishedState($service);
        $service->saveQuietly();
        $this->deleteSchedules($service);
    }


    /**
     * @param \App\Models\Service $service
     */
    private function clearPublishedState(Service $service): void {
        $service->forceFill(['is_published' => false]);
    }


    /**
     * @param \App\Models\Service $service
     */
    private function unpublishSchedules(Service $service): void {
        $service->schedules()->published()->get()->each(static function($schedule, $key) {
            $schedule->is_published = true;
            $schedule->save();
        });
    }


    /**
     * @param \App\Models\Service $service
     */
    private function deleteSchedules(Service $service): void {
        $service->schedules()->get()->each(static function($schedule, $key) {
            $schedule->delete();
        });
    }


}
