<?php

namespace App\Listeners;

use App\Events\AppointmentBooked;
use App\Helpers\GoogleCalendarHelper;
use Illuminate\Support\Facades\Log;

class AppointmentBookedEventHandler
{
    public function handle(AppointmentBooked $event): void
    {
        if (!$event->calendar || !$event->calendar->calendar_id) {
            return;
        }

        try {
            $gcHelper = new GoogleCalendarHelper($event->calendar);
            $gcHelper->setEvent($event);
        } catch (\Exception $e) {
            Log::channel('google_calendar_failed')->error('[EXCEPTION] Booking Appointment event handler', [
                'booking_id'  => $event->booking->id,
                'calendar_id' => $event->calendar->calendar_id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
