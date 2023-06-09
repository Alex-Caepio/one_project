<?php

namespace App\Actions\RescheduleRequest;

use App\Models\Booking;
use App\Models\Price;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RescheduleRequestStore
{
    public function execute(Booking $booking, Request $request): RescheduleRequest
    {
        $rescheduleRequest = new RescheduleRequest();
        /** @var Schedule $newSchedule */
        $newSchedule = Schedule::find($request->get('new_schedule_id'));
        /** @var Schedule $oldSchedule */
        $oldSchedule = Schedule::find($booking->schedule_id);

        $data = [
            'user_id'         => $booking->user_id,
            'booking_id'      => $booking->id,
            'schedule_id'     => $booking->schedule_id,
            'new_schedule_id' => $request->get('new_schedule_id'),
            // Receive prices except services of the bespoke type.
            'new_price_id'    => $booking->schedule->service->service_type_id !== Service::TYPE_BESPOKE
                ? $request->get('new_price_id')
                : $booking->price_id,
            'comment'         => $request->get('comment'),
            'old_price_id'    => $booking->price_id,
            'requested_by'    => $request->user()->id === $booking->user_id
                ? RescheduleRequest::REQUESTED_BY_CLIENT
                : RescheduleRequest::REQUESTED_BY_PRACTITIONER,
        ];

        if ($newSchedule->location_displayed !== $oldSchedule->location_displayed) {
            $data['old_location_displayed'] = $oldSchedule->location_displayed;
            $data['new_location_displayed'] = $newSchedule->location_displayed;
        }

        if ($newSchedule->start_date !== $oldSchedule->start_date) {
            $data['old_start_date'] = $oldSchedule->start_date;
            $data['new_start_date'] = $newSchedule->start_date;
        }

        if ($newSchedule->end_date !== $oldSchedule->end_date) {
            $data['old_end_date'] = $oldSchedule->end_date;
            $data['new_end_date'] = $newSchedule->end_date;
        }

        if ($newSchedule->url !== $oldSchedule->url) {
            $data['old_url'] = $oldSchedule->url;
            $data['new_url'] = $newSchedule->url;
        }

        if (
            $booking->schedule->service->service_type_id === Service::TYPE_APPOINTMENT
            && $request->has('availabilities.0.datetime_from')
        ) {
            /** @var Price $price */
            $price = $newSchedule->prices()->where('id', $request->get('new_price_id'))->first();
            /** @var string $availability */
            $datetimeFrom = $request->input('availabilities.0.datetime_from');
            $data['old_start_date'] = $booking->datetime_from;
            $data['old_end_date'] = $booking->datetime_to;
            $data['new_start_date'] = $datetimeFrom;
            $data['new_end_date'] =  (new Carbon($datetimeFrom))->addMinutes($price->duration);
        }

        $rescheduleRequest->forceFill($data);
        $rescheduleRequest->save();

        return $rescheduleRequest;
    }
}
