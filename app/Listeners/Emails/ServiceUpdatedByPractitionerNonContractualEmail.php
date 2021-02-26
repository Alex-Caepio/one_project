<?php

namespace App\Listeners\Emails;

use App\Events\ServiceUpdatedByPractitionerNonContractual;
use App\Models\Booking;
use Carbon\Carbon;


class ServiceUpdatedByPractitionerNonContractualEmail extends SendEmailHandler {

    public function handle(ServiceUpdatedByPractitionerNonContractual $event): void {
        $this->templateName = 'Service Updated by Practitioner (Non-Contractual)';
        $this->event = $event;

        $upcomingBookings = Booking::where('schedule_id', $this->event->schedule->id)->whereNull('cancelled_at')
                                   ->where('date_from', '>=', Carbon::now())->with([
                                                                                       'user',
                                                                                       'practitioner',
                                                                                       'schedule',
                                                                                       'schedule.service'
                                                                                   ])->get();
        foreach ($upcomingBookings as $booking) {
            $this->event->fillEvent($booking);
            $this->toEmail = $this->event->user->email;
            $this->sendCustomEmail();
        }

    }
}
