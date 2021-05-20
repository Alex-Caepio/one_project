<?php


namespace App\Actions\RescheduleRequest;

use App\Actions\Cancellation\CancelBooking;
use App\Events\RescheduleRequestDeclinedByClient;
use App\Models\Booking;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RescheduleRequestDecline {

    public function execute(RescheduleRequest $rescheduleRequest): void {
        event(new RescheduleRequestDeclinedByClient($rescheduleRequest));

        // declined by client of the booking
        if ($rescheduleRequest->requested_by === User::ACCOUNT_PRACTITIONER &&
            (int)$rescheduleRequest->schedule_id === (int)$rescheduleRequest->new_schedule_id
            && $rescheduleRequest->user_id === Auth::id()) {
            run_action(CancelBooking::class, $rescheduleRequest->booking,
                       true);
        }
        $rescheduleRequest->delete();
    }
}
