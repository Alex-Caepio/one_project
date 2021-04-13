<?php

namespace App\Http\Controllers\Admin;

use App\Filters\BookingFilters;
use App\Http\Requests\Request;
use App\Models\Booking;
use App\Models\User;
use App\Transformers\BookingTransformer;
use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;

class BookingController extends Controller
{
    public function index(Request $request, BookingFilters $filters)
    {
        $query = Booking::filter($filters);

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $query->orderBy($order['column'], $order['direction']);
        }

        if ($request->hasSearch()) {
            $search = $request->search();

            $query->where(
                function ($query) use ($search) {
                    $query->where('reference', 'like', "%{$search}%")
                        ->orWhereHas('practitioner', function($q) use ($search){
                            $q->where('business_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('user', function($q) use ($search){
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });
        }

        $paginator = $query->paginate($request->getLimit());
        $booking   = $paginator->getCollection();

        return response(fractal($booking, new BookingTransformer())
            ->parseIncludes($request->getIncludes())->toArray())
            ->withPaginationHeaders($paginator);

    }
}
