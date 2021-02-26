<?php

namespace App\Listeners\Emails;

use App\Events\ServiceUpdatedByPractitionerContractual;
use App\Models\Booking;
use Carbon\Carbon;


class ServiceUpdatedByPractitionerContractualEmail {

    public function handle(ServiceUpdatedByPractitionerContractual $event): void {
        $this->templateName = 'Service Updated by Practitioner (Contractual)';
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
