<?php

namespace App\Http\Controllers\Admin;

use App\Filters\BookingFilters;
use App\Http\Requests\Request;
use App\Models\Booking;
use App\Transformers\BookingTransformer;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    public function index(Request $request, BookingFilters $filters)
    {
        $query = Booking::filter($filters);

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $query->orderBy($order['column'], $order['direction']);
        }

        $paginator = $query->paginate();
        $booking   = $paginator->getCollection();

        return response(fractal($booking, new BookingTransformer())
            ->parseIncludes($request->getIncludes())->toArray())
            ->withPaginationHeaders($paginator);

    }
}
