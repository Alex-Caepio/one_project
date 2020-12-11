<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reschedule\RescheduleRequestRequest;
use App\Http\Requests\Request;
use App\Models\Booking;
use App\Models\RescheduleRequest;
use App\Transformers\BookingTransformer;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $paginator = Booking::where('user_id', $request->user()->id)->paginate($request->getLimit());
        $booking   = $paginator->getCollection();

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $booking->orderBy($order['column'], $order['direction']);
        }

        return response(fractal($booking, new BookingTransformer())
            ->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);

    }

    public function reschedule(RescheduleRequestRequest $request, Booking $booking)
    {
        $rescheduleRequest = new RescheduleRequest();
        $rescheduleRequest->forceFill(
            [
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'schedule_id' => $booking->schedule_id,
                'new_schedule_id' => $request->get('new_schedule_id'),
                'new_price_id' => $request->get('new_price_id'),
                'new_datetime_from' => $request->get('new_datetime_from'),
                'comment' => $request->get('comment'),
            ]
        );
        $rescheduleRequest->save();

        return response(null, 200);
    }

}
