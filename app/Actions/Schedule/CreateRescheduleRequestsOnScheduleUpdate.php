<?php

namespace App\Actions\Schedule;

use App\Http\Requests\Request;
use App\Models\Booking;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use Carbon\Carbon;

class CreateRescheduleRequestsOnScheduleUpdate
{
    public function execute(Request $request, Schedule $schedule){

        if ($this->requiresReschedule($request, $schedule)) {
            //should be moved to constant
            $bookings = $schedule->service->service_type == 'appointment'
                ? $schedule->getOutsiderBookings()
                : Booking::where('schedule_id', $schedule->id)->get();

            //In order to avoid duplicated reschedule request we have to delete all prevous first
            $schedule->rescheduleRequests()->whereIn('booking_id', $bookings->pluck('id'))->delete();

            $rescheduleRequests = [];
            foreach ($bookings as $booking) {
                $rescheduleRequests[] = [
                    'user_id' => $booking->user_id,
                    'booking_id' => $booking->id,
                    'schedule_id' => $booking->schedule_id,
                    'new_schedule_id' => $schedule->id,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s')
                ];
            }

            if ($this->locationHasChanged($request, $schedule)) {
                foreach ($rescheduleRequests as $key => $reschedule) {
                    $rescheduleRequests[$key]['old_location_displayed'] = $schedule->location_displayed;
                    $rescheduleRequests[$key]['new_location_displayed'] = $request->get('location_displayed');
                }
            }

            if ($this->dateHasChanged($request, $schedule)) {
                foreach ($rescheduleRequests as $key => $reschedule) {
                    $rescheduleRequests[$key]['old_start_date'] = $schedule->start_date;
                    $rescheduleRequests[$key]['new_start_date'] = $request->get('start_date');
                    $rescheduleRequests[$key]['old_end_date'] = $schedule->end_date;
                    $rescheduleRequests[$key]['new_end_date'] = $request->get('end_date');
                }
            }

            RescheduleRequest::insert($rescheduleRequests);
        }
    }

    protected function requiresReschedule(Request $request, Schedule $schedule): bool
    {
        return $this->dateHasChanged($request, $schedule)
            || $this->locationHasChanged($request, $schedule)
            || $request->filled('schedule_unavailabilities')
            || $request->filled('schedule_availabilities');
    }

    protected function dateHasChanged(Request $request, Schedule $schedule): bool
    {
        if ($request['start_date'] != $schedule->start_date || $request['end_date'] != $schedule->end_date) {
            return true;
        }

        return false;
    }

    protected function locationHasChanged(Request $request, Schedule $schedule): bool
    {
        if (
            $request['location_id'] != $schedule->location_id
            || $request['venue'] != $schedule->venue
            || $request['city'] != $schedule->city
            || $request['country'] != $schedule->country
            || $request['post_code'] != $schedule->post_code
            || $request['location_displayed'] != $schedule->location_displayed
            || $request['is_virtual'] != $schedule->is_virtual
        ) {
            return true;
            }

        return false;
    }
}
