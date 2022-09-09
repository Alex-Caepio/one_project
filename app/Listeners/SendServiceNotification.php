<?php

namespace App\Listeners;

use App\Events\ServiceUpdated;
use App\Events\ServiceUpdatedNonContractual;
use App\Models\Service;
use DateTime;

/**
 * Handles events and sends an email when a service has been updated and has
 * specific conditions.
 */
class SendServiceNotification
{
    public function handle(ServiceUpdated $event)
    {
        $lastPublish = new DateTime(date(
            'Y-m-d H:i:s',
            strtotime('+5 minutes', strtotime($event->service->last_published))
        ));
        $currentDate = new DateTime('now');

        if (
            $event->service->hasUpdates()
            && !$event->service->visibilityChanged()
            && !in_array($event->service->service_type->id, [Service::TYPE_BESPOKE])
            && $lastPublish < $currentDate
        ) {
            event(new ServiceUpdatedNonContractual($event->service));
        }
    }
}
