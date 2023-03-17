<?php

namespace App\Observers;

use App\Events\ServiceListingLive;
use App\Events\ServiceUnpublished;
use App\Models\Service;
use Carbon\Carbon;

class ServiceObserver
{
    /**
     * Handle the article "updated" event
     */
    public function saved(Service $service): void
    {
        if (!$service->isDirty('is_published')) {
            return;
        }

        if (!$service->is_published && !$service->wasRecentlyCreated) {
            $this->unpublishSchedules($service);
            event(new ServiceUnpublished($service));
        } elseif ($service->is_published) {
            event(new ServiceListingLive($service));
        }
    }

    public function saving(Service $service): void
    {
        if (!$service->isDirty('is_published')) {
            return;
        }

        if (!$service->is_published) {
            $this->clearPublishedState($service);

            return;
        }

        $publishedDate = Carbon::now()->format('Y-m-d H:i:s');
        $service->last_published = $publishedDate;

        if (!$service->getOriginal('published_at')) {
            $service->published_at = $publishedDate;
        }
    }

    /**
     * Handle the article "deleting" event.
     * Drop Published Fields.
     */
    public function deleting(Service $service): void
    {
        $this->clearPublishedState($service);
        $service->saveQuietly();
        $this->deleteSchedules($service);
    }

    private function clearPublishedState(Service $service): void
    {
        $service->forceFill(['is_published' => false]);
    }

    private function unpublishSchedules(Service $service): void
    {
        $service
            ->schedules()
            ->get()
            ->each(static function ($schedule, $key) {
                $schedule->is_published = false;
                $schedule->save();
            });
    }

    private function deleteSchedules(Service $service): void
    {
        $service
            ->schedules()
            ->get()
            ->each(static function ($schedule, $key) {
                $schedule->delete();
            });
    }
}
