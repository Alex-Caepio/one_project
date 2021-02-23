<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Purchase;
use App\Http\Requests\Request;
use App\Transformers\MyClientPurchaseTransformer;
use App\Transformers\MyClientTransformer;
use Illuminate\Support\Facades\DB;

class BookingMyClientController extends Controller
{

    public function index(Request $request)
    {
        $query = User::query()->selectRaw(
            'users.*,
            count(distinct live_bookings.id) live_bookings,
            count(distinct attended_bookings.id) attended_bookings,
            count(distinct bookings.id) bookings,
            max(bookings.created_at) last_purchase,
            max(attended_bookings.created_at) last_service
            '
        )
            ->join('bookings', 'bookings.user_id', '=', 'users.id')
            ->leftJoin('bookings as live_bookings',
                function ($q) {
                    $q->on('live_bookings.id', '=', 'bookings.id')
                        ->on('live_bookings.datetime_from', '>', DB::raw('now()'));
                })
            ->leftJoin('bookings as attended_bookings',
                function ($q) {
                    $q->on('attended_bookings.id', '=', 'bookings.id')
                        ->on('attended_bookings.datetime_from', '<=', DB::raw('now()'));
                })
            ->join('schedules', 'schedules.id', '=', 'bookings.schedule_id')
            ->join('services', 'services.id', '=', 'schedules.service_id')
            ->where('services.user_id', $request->user()->id)
            ->groupBy('users.id');

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $query->orderBy($order['column'], $order['direction']);
        }

        $paginator = $query->paginate($request->getLimit());
        $myClients = $paginator->getCollection();

        return response(fractal($myClients, new MyClientTransformer())
            ->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);
    }

    public function purchases(Request $request)
    {
        $paginator = Purchase::query()
            ->selectRaw(implode(', ', [
                'purchases.id as id',
                'services.title as service_name',
                'service_types.name as service_type',
                'schedules.title as schedule_name',
                'purchases.created_at as purchase_date',
                'concat(users.first_name, " ", users.last_name) as client',
                'purchases.price as paid',
                'schedules.location_displayed as location',
                'schedules.refund_terms as refund_terms',
            ]))
            ->join('services', 'services.id', '=', 'purchases.service_id')
            ->join('service_types', 'service_types.id', '=', 'services.service_type_id')
            ->join('schedules', 'schedules.id', '=', 'purchases.schedule_id')
            ->join('users', 'users.id', '=', 'purchases.user_id')
            ->where('services.user_id', $request->user()->id)
            ->paginate($request->getLimit());

        $purchases = $paginator->getCollection();

        return response(fractal($purchases, new MyClientPurchaseTransformer())
            ->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);
    }
}
