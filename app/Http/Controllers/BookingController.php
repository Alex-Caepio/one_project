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

    public function show(Booking $booking, Request $request)
    {
        return fractal($booking, new BookingTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function reschedule(RescheduleRequestRequest $request, Booking $booking)
    {
        $rescheduleRequest = new RescheduleRequest();
        RescheduleRequest::where('booking_id', $booking->id)->delete();

        $rescheduleRequest->forceFill(
            [
                'user_id'         => $booking->user_id,
                'booking_id'      => $booking->id,
                'schedule_id'     => $booking->schedule_id,
                'new_schedule_id' => $request->get('new_schedule_id'),
                'new_price_id'    => $request->get('new_price_id'),
                'comment'         => $request->get('comment'),
                'old_price_id'    => $booking->price_id,
                'requested_by'    => $request->user()->id == $booking->user_id ? 'client' : 'practitioner',
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
