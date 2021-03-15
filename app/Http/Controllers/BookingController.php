<?php

namespace App\Http\Controllers;

use App\Filters\BookingFilters;
use App\Http\Requests\Bookings\BookingCompleteRequest;
use App\Http\Requests\Reschedule\RescheduleRequestRequest;
use App\Http\Requests\Request;
use App\Models\Booking;
use App\Models\RescheduleRequest;
use App\Transformers\BookingTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller {
    public function index(Request $request, BookingFilters $filters) {
        $Query = Booking::filter($filters)->where('user_id', $request->user()->id)->with($request->getIncludes());

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $Query->orderBy($order['column'], $order['direction']);
        }

        $paginator = $Query->paginate($request->getLimit());
        $booking = $paginator->getCollection();

        return response(fractal($booking,
                                new BookingTransformer())->parseIncludes($request->getIncludes()))->withPaginationHeaders($paginator);

    }

    public function show(Booking $booking, Request $request) {
        return fractal($booking, new BookingTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function complete(Booking $booking, BookingCompleteRequest $request) {
        $booking->status = 'completed';
        $booking->save();

        return response(null, 200);
    }
}
