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

        $query->orderBy('bookings.datetime_from', 'DESC');

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $query->orderBy($order['column'], $order['direction']);
        }

        if ($filters->hasUpcomingStatus()) {
            $query->whereRaw('(DATEDIFF(bookings.datetime_from, NOW()) >= 0 OR bookings.datetime_from = bookings.datetime_to)');
        }

        $paginator = $query->paginate($request->getLimit());
        $booking = $paginator->getCollection();

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
                if ($bookingForClient) {
                    $rrQuery->whereIn('requested_by', RescheduleRequest::getPractitionerRequestValues());
                } else {
                    $rrQuery->where('requested_by', RescheduleRequest::REQUESTED_BY_CLIENT);
                }
            }
        ]);

        return fractal($booking, new BookingTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function complete(Booking $booking, BookingCompleteRequest $request)
    {
        $booking->status = Booking::COMPLETED_STATUS;
        $booking->save();
        Notification::where('booking_id', $booking->id)->delete();
        RescheduleRequest::where('booking_id', $booking->id)->delete();

        return response(null, 200);
    }
}
