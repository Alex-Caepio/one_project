<?php

namespace App\Http\Controllers;

use App\Filters\BookingFilters;
use App\Http\Requests\Bookings\BookingCompleteRequest;
use App\Http\Requests\Request;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\RescheduleRequest;
use App\Transformers\BookingTransformer;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index(Request $request, BookingFilters $filters)
    {
        $query = Booking::query()
            ->filter($filters)
            ->where('user_id', $request->user()->id)
            ->with(
                $request->getIncludesWithTrashed([
                    'schedule',
                    'schedule.service',
                    'practitioner',
                    'schedule.service.practitioner',
                    'snapshot',
                ])
            );

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $query->orderBy($order['column'], $order['direction']);
        }

        $query->selectRaw('*, DATEDIFF(bookings.datetime_from, NOW()) as date_diff')->orderByRaw('ABS(date_diff)');

        if ($filters->hasUpcomingStatus()) {
            $query->having('date_diff', '>=', 0);
        }

        $paginator = $query->paginate($request->getLimit());
        $booking = $paginator->getCollection()->toSnapshots();

        return response(fractal($booking, new BookingTransformer())->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);
    }

    public function show(Booking $booking, Request $request)
    {
        $booking = $booking->snapshot ?? $booking;

        $booking->load([
            'schedule' => static function ($scheduleQuery) {
                $scheduleQuery->withTrashed();
            },
            'schedule.service' => static function ($serviceQuery) {
                $serviceQuery->withTrashed();
            }
        ]);

        //load only practitioner reschedule
        $bookingForClient = Auth::user()->id === $booking->user_id;
        $booking->load([
            'reschedule_requests' => static function ($rrQuery) use ($bookingForClient) {
                $rrQuery->where('requested_by', $bookingForClient ? 'practitioner' : 'client');
            }
        ]);

        return fractal($booking, new BookingTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function complete(Booking $booking, BookingCompleteRequest $request)
    {
        $booking->status = 'completed';
        $booking->save();
        Notification::where('booking_id', $booking->id)->delete();
        RescheduleRequest::where('booking_id', $booking->id)->delete();

        return response(null, 200);
    }
}
