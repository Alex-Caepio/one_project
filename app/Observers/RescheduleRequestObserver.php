<?php


namespace App\Observers;

use App\Events\BookingRescheduleOfferedByPractitioner;
use App\Models\RescheduleRequest;
use App\Models\User;

class RescheduleRequestObserver {

    /**
     * Handle booking created.
     *
     * @param \App\Models\RescheduleRequest $rescheduleRequest
     * @return void
     */
    public function created(RescheduleRequest $rescheduleRequest): void {
        if ($rescheduleRequest->requested_by === User::ACCOUNT_PRACTITIONER) {
            event(new BookingRescheduleOfferedByPractitioner($rescheduleRequest));
        }
    }

}
