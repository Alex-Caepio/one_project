<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\Schedule;
use App\Transformers\BookingTransformer;

class ScheduleBookingController extends Controller
{
    public function index(Schedule $schedule, Request $request)
    {
        $scheduleBookings = $schedule->bookings()
            ->where('datetime_from', '>', now())
            ->with($request->getIncludes())->get();

        return fractal($scheduleBookings, new BookingTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }
}
