<?php

namespace App\Http\Controllers;


use App\Http\Requests\Request;
use App\Models\Booking;
use App\Models\User;
use App\Transformers\BookingTransformer;
use App\Transformers\MyClientTransformer;
use Illuminate\Support\Facades\DB;

class BookingMyClientController extends Controller
{

    public function index(Request $request)
    {
        $paginator = User::query()->selectRaw(
            'users.*,
            count(distinct live_bookings.id) live_bookings,
            count(distinct attended_bookings.id) attended_bookings,
            count(distinct bookings.id) bookings,
            max(bookings.created_at) last_purchase,
            max(attended_bookings.created_at) last_service
            '
        )
            ->join('bookings', 'bookings.user_id', '=', 'users.id')
            ->leftJoin('bookings as live_bookings',
                function ($q) {
                    $q->on('live_bookings.id', '=', 'bookings.id')
                        ->on('live_bookings.datetime_from', '>', DB::raw('now()'));
                })
            ->leftJoin('bookings as attended_bookings',
                function ($q) {
                    $q->on('attended_bookings.id', '=', 'bookings.id')
                        ->on('attended_bookings.datetime_from', '<=', DB::raw('now()'));
                })
            ->join('schedules', 'schedules.id', '=', 'bookings.schedule_id')
            ->join('services', 'services.id', '=', 'schedules.service_id')
            ->where('services.user_id', $request->user()->id)
            ->groupBy('users.id')
            ->paginate($request->getLimit());

        $myClients    = $paginator->getCollection();

        return response(fractal($myClients, new MyClientTransformer())
            ->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);
    }
}
