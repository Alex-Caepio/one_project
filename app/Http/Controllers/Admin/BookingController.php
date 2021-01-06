<?php

namespace App\Http\Controllers\Admin;

use App\Filters\BookingFilters;
use App\Http\Requests\Request;
use App\Models\Booking;
use App\Transformers\BookingTransformer;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    public function index(Request $request,BookingFilters $filters)
    {
        $Query = Booking::filter($filters);

        if ($request->hasOrderBy())
        {
            $order = $request->getOrderBy();
            $Query->orderBy($order['column'], $order['direction']);
        }

        $paginator = $Query->paginate();
        $booking   = $paginator->getCollection();

        return response(fractal($booking, new BookingTransformer())
            ->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);

    }
}
