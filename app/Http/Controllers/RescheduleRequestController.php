<?php

namespace App\Http\Controllers;

use App\Actions\RescheduleRequest\RescheduleRequestStore;
use App\Http\Requests\Request;
use App\Http\Requests\Reschedule\RescheduleRequestRequest;
use App\Models\Booking;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class RescheduleRequestController extends Controller
{
    public function index()
    {
        $user = Auth::id();
        $rescheduleRequest = RescheduleRequest::all()->where('user_id', $user);
        return response($rescheduleRequest);
    }

    public function store(Schedule $schedule, Request $request)
    {
        $user = Auth::id();
        RescheduleRequest::where('schedule_id', $schedule->id)->where('user_id', $user)->delete();

        run_action(RescheduleRequestStore::class, $schedule, $request);
    }

    public function accept(RescheduleRequest $rescheduleRequest)
    {
        $booking = $rescheduleRequest->booking;
        $booking->schedule_id = $rescheduleRequest->new_schedule_id;
        $booking->datetime_from = $rescheduleRequest->new_start_date;
        $booking->datetime_to = $rescheduleRequest->new_end_date;

        $booking->update();
        $rescheduleRequest->delete();

        return response(null, 204);
    }

    public function decline(RescheduleRequest $rescheduleRequest)
    {
        $rescheduleRequest->delete();
        return response(null, 204);
    }
}
