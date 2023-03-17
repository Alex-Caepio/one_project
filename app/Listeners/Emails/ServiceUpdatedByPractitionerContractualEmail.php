<?php

namespace App\Listeners\Emails;

use App\Events\ServiceUpdatedByPractitionerContractual;
use App\Models\Booking;
use Carbon\Carbon;

class ServiceUpdatedByPractitionerContractualEmail extends SendEmailHandler
{
    public function handle(ServiceUpdatedByPractitionerContractual $event): void
    {
        $this->templateName = 'Service Updated by Practitioner (Contractual)';
        $this->event = $event;

        /** @var Booking[] $upcomingBookings */
        $upcomingBookings = Booking::query()
            ->where('schedule_id', $this->event->schedule->id)
            ->active()
            ->has('practitioner_reschedule_request')
            ->where('datetime_from', '>=', Carbon::now())
            ->with(array_merge([
                'practitioner_reschedule_request'],
                $this->event->getBookingDependencies()
            ))
            ->get()
        ;

        foreach ($upcomingBookings as $booking) {
            $this->event->setBooking($booking);
            $this->event->reschedule = $booking->practitioner_reschedule_request;
            $this->toEmail = $this->event->user->email;
            $this->sendCustomEmail();
        }
    }
}
