<?php


namespace App\Actions\RescheduleRequest;

use App\Models\Booking;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\User;
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
            'new_price_id'    => $request->get('new_price_id'),
            'comment'         => $request->get('comment'),
            'old_price_id'    => $booking->price_id,
            'requested_by'    => $request->user()->id === $booking->user_id
                ? User::ACCOUNT_CLIENT
                : User::ACCOUNT_PRACTITIONER,
        ];

        if ($newSchedule->location_displayed !== $oldSchedule->location_displayed) {
            $data['old_location_displayed'] = $oldSchedule->location_dislpayed;
            $data['new_location_displayed'] = $newSchedule->location_dislpayed;
        }

        if ($newSchedule->start_date !== $oldSchedule->start_date) {
            $data['old_start_date'] = $oldSchedule->start_date;
            $data['new_start_date'] = $newSchedule->start_date;
        }

        if ($newSchedule->end_date !== $oldSchedule->end_date) {
            $data['old_end_date'] = $oldSchedule->end_date;
            $data['new_end_date'] = $newSchedule->end_date;
        }

        $rescheduleRequest->forceFill($data);
        $rescheduleRequest->save();

        return $rescheduleRequest;
    }
}
