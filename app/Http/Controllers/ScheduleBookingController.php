<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Http\Requests\Request;
use App\Transformers\UpcomingBookingTransformer;

class ScheduleBookingController extends Controller
{
    public function index(Schedule $schedule, Request $request)
    {
        $paginator = $schedule->bookings()
            ->selectRaw('
                bookings.*,
                count(distinct fullpaidbookings.id) as full_paid_bookings,
                count(distinct subbookings.id) as price_bookings,
                prices.number_available as number_available
            ')
            ->leftJoin('prices', 'bookings.price_id', '=', 'prices.id')
            ->leftJoin('bookings as subbookings', 'prices.id', '=', 'subbookings.price_id')
            ->leftJoin('bookings as fullpaidbookings', function($join){
                $join->on('prices.id', '=', 'fullpaidbookings.price_id')
                    ->where('fullpaidbookings.is_installment', '=', false);
            })
            ->where('bookings.datetime_from', '>', now())
            ->groupBy('bookings.id')
            ->with($request->getIncludes())
            ->paginate($request->getLimit());

        $scheduleBookings = $paginator->getCollection();

        return response(fractal($scheduleBookings, new UpcomingBookingTransformer())
            ->parseIncludes($request->getIncludes())->toArray())
            ->withPaginationHeaders($paginator);
    }
}
