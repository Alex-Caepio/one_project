<?php

namespace App\Listeners;

use App\Events\ServiceUpdated;
use App\Events\ServiceUpdatedNonContractual;
use App\Models\Service;

/**
 * Handles events and sends an email when a service has been updated and has
 * specific conditions.
 */
class SendServiceNotification
{
    public function handle(ServiceUpdated $event)
    {
        if (
            $event->service->hasUpdates()
            && !$event->service->visibilityChanged()
            && !in_array($event->service->service_type->id, [Service::TYPE_BESPOKE])
        ) {
            event(new ServiceUpdatedNonContractual($event->service));
        }
    }
}
