<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Http\Requests\Request;
use App\Transformers\BookingTransformer;

class ScheduleBookingController extends Controller
{
    public function index(Schedule $schedule, Request $request)
    {
        $paginator = $schedule->bookings()
            ->where('datetime_from', '>', now())
            ->with($request->getIncludes())
            ->paginate($request->getLimit());

        $scheduleBookings = $paginator->getCollection();

        return response(fractal($scheduleBookings, new BookingTransformer())
            ->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);
    }
}
