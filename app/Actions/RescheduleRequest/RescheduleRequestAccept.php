<?php

namespace App\Actions\RescheduleRequest;

use App\Events\BookingRescheduleAcceptedByClient;
use App\Models\Booking;
use App\Models\Price;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;

class RescheduleRequestAccept
{
    public function execute(RescheduleRequest $rescheduleRequest, bool $informPractitioner = true): void
    {
        /** @var Booking $booking */
        $booking = $rescheduleRequest->booking;
        $booking->schedule_id = $rescheduleRequest->new_schedule_id;
        $booking->datetime_from = $rescheduleRequest->new_start_date;
        $booking->datetime_to = $rescheduleRequest->new_end_date;
        $booking->status = Booking::RESCHEDULED_STATUS;

        $newSchedule = Schedule::find($rescheduleRequest->new_schedule_id);

        if ($newSchedule->service->service_type_id === Service::TYPE_APPOINTMENT) {
            /** @var Price $price */
            $price = $newSchedule->prices()->where('id', $rescheduleRequest->get('price_id'))->first();
            /** @var string $availability */
            $datetimeFrom = $rescheduleRequest->get('availabilities.0.datetime_from');
            $booking->datetime_from = $datetimeFrom;
            $booking->datetime_to = (new Carbon($datetimeFrom))->addMinutes($price->duration);
        }

        $booking->update();
        event(new BookingRescheduleAcceptedByClient($booking, $informPractitioner));
        $rescheduleRequest->delete();
    }
}
