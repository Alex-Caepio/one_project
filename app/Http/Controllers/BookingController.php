<?php

namespace App\Http\Controllers;

use App\Filters\BookingFilters;
use App\Http\Requests\Reschedule\RescheduleRequestRequest;
use App\Http\Requests\Request;
use App\Models\Booking;
use App\Models\RescheduleRequest;
use App\Transformers\BookingTransformer;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request, BookingFilters $filters)
    {
        $Query = Booking::filter($filters)
            ->where('user_id', $request->user()->id)
            ->with($request->getIncludes());

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $Query->orderBy($order['column'], $order['direction']);
        }

        $paginator = $Query->paginate($request->getLimit());
        $booking   = $paginator->getCollection();

        return response(fractal($booking, new BookingTransformer())
            ->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);

    }

    public function reschedule(RescheduleRequestRequest $request, Booking $booking)
    {
        $rescheduleRequest = new RescheduleRequest();

        $bookingsId = $request->get('booking_ids');
        RescheduleRequest::whereIn('booking_id', $bookingsId)->delete();

        $rescheduleRequest->forceFill(
            [
                'user_id'         => $booking->user_id,
                'booking_id'      => $booking->id,
                'schedule_id'     => $booking->schedule_id,
                'new_schedule_id' => $request->get('new_schedule_id'),
                'new_price_id'    => $request->get('new_price_id'),
                'comment'         => $request->get('comment'),
            ]
        );
        $rescheduleRequest->save();

        return response(null, 200);
    }

    public function allReschedule(RescheduleRequestRequest $request, Booking $booking)
    {
        $booking_ids        = $booking->whereIn('id', $request->booking_ids)->get();
        $rescheduleRequests = [];
        foreach ($booking_ids as $booking_id) {

            $rescheduleRequests[] = [
                'user_id'         => $booking_id->user_id,
                'booking_id'      => $booking_id->id,
                'schedule_id'     => $booking_id->schedule_id,
                'new_schedule_id' => $request->get('new_schedule_id'),
                'new_price_id'    => $request->get('new_price_id'),
                'comment'         => $request->get('comment'),
                'created_at'      => Carbon::now()->format('Y-m-d H:i:s')
            ];
        }
        RescheduleRequest::insert($rescheduleRequests);

        return response(null, 200);
    }
}
