<?php

namespace App\Listeners\Emails;

use App\Events\ServiceUpdatedByPractitionerContractual;
use App\Models\Booking;
use Carbon\Carbon;


class ServiceUpdatedByPractitionerContractualEmail extends SendEmailHandler {

    public function handle(ServiceUpdatedByPractitionerContractual $event): void {
        $this->templateName = 'Service Updated by Practitioner (Contractual)';
        $this->event = $event;

        $upcomingBookings =
            Booking::where('schedule_id', $this->event->schedule->id)->active()->has('practitioner_reschedule_request')
                   ->where('datetime_from', '>=', Carbon::now())->with([
                                                                           'user',
                                                                           'practitioner',
                                                                           'schedule',
                                                                           'schedule.service',
                                                                           'practitioner_reschedule_request'
                                                                       ])->get();
        foreach ($upcomingBookings as $booking) {
            $this->event->booking = $booking;
            $this->event->reschedule = $booking->practitioner_reschedule_request;
            $this->event->fillEvent();
            $this->toEmail = $this->event->user->email;
            $this->sendCustomEmail();
        }

    }
}
