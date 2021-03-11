<?php


namespace App\Actions\RescheduleRequest;

use App\Http\Requests\Request;
use App\Models\Booking;
use App\Models\RescheduleRequest;

class RescheduleRequestStore {
    public function execute(Booking $booking, Request $request) {
        $rescheduleRequest = new RescheduleRequest();
        $rescheduleRequest->forceFill([
                                          'user_id'         => $booking->user_id,
                                          'booking_id'      => $booking->id,
                                          'schedule_id'     => $booking->schedule_id,
                                          'new_schedule_id' => $request->get('new_schedule_id'),
                                          'new_price_id'    => $request->get('new_price_id'),
                                          'comment'         => $request->get('comment'),
                                          'old_price_id'    => $booking->price_id,
                                          'requested_by'    => $request->user()->id ===
                                                               $booking->user_id ? 'client' : 'practitioner',
                                      ]);
        $rescheduleRequest->save();
    }
}
