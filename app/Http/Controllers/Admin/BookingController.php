<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Request;
use App\Models\Booking;
use App\Transformers\BookingTransformer;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $paginator = Booking::query()->paginate($request->getLimit());
        $booking   = $paginator->getCollection();

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $booking->orderBy($order['column'], $order['direction']);
        }

        return response(fractal($booking, new BookingTransformer())
            ->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);

    }
}
