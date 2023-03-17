<?php

namespace App\Observers;

use App\Events\BookingRescheduleOfferedByPractitioner;
use App\Events\ClientRescheduledFyi;
use App\Models\RescheduleRequest;

class RescheduleRequestObserver
{
    /**
     * Handle booking created.
     */
    public function created(RescheduleRequest $rescheduleRequest): void
    {
        if (in_array($rescheduleRequest->requested_by, RescheduleRequest::getPractitionerRequestValues(), true)) {
            event(new BookingRescheduleOfferedByPractitioner($rescheduleRequest));
        } elseif ($rescheduleRequest->requested_by === RescheduleRequest::REQUESTED_BY_CLIENT) {
            event(new ClientRescheduledFyi($rescheduleRequest));
        }
    }
}
