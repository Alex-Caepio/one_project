<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingView;
use App\Models\Schedule;
use App\Models\Purchase;
use App\Http\Requests\Request;
use App\Transformers\MyClientClosedTransformer;
use App\Transformers\MyClientPurchaseTransformer;
use App\Transformers\MyClientTransformer;
use App\Transformers\MyClientUpcomingTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingMyClientController extends Controller
{

    public function index(Request $request)
    {
        $query = BookingView::query()->with(['user'])
            ->selectRaw(
                '*,
                count(*) as live_bookings,
                max(created_at) as last_purchase,
                max(datetime_from) as last_service'
            )
            ->where('practitioner_id', Auth::id())
            ->where(function (Builder $builder) {
                return $builder
                    ->where(function (Builder $builder) {
                        return $builder
                            ->where('service_type_id', '=', BookingView::BESPOKE_SERVICE_VALUE)
                            ->whereIn('status', BookingView::LIVE_BOOKING_STATUS);
                    })
                    ->orWhere(function (Builder $builder) {
                        return $builder
                            ->where('service_type_id', '!=', BookingView::BESPOKE_SERVICE_VALUE)
                            ->where('datetime_from', '>', DB::raw('now()'));
                    });
            })
            ->groupBy('id');


        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $query->orderBy($order['column'], $order['direction']);
        }

        $paginator = $query->paginate($request->getLimit());
        $myClients = $paginator->getCollection();

        return response(
            fractal(
                $myClients,
                new MyClientTransformer()
            )->parseIncludes($request->getIncludes())
        )->withPaginationHeaders($paginator);
    }

    public function upcoming(Request $request)
    {
        $paginator = Schedule::query()
            ->selectRaw(
                implode(', ', [
                    'schedules.id as id',
                    'services.id as service_id',
                    'services.title as service_name',
                    'service_types.name as service_type',
                    'schedules.title as schedule_name',
                    'schedules.start_date as start_datetime',
                    'concat(sum(bookings.amount), " of ", schedules.attendees) as bookings',
                    'concat(SUM(case when bookings.is_fully_paid = 1 then bookings.amount else 0 end), " of ", sum(bookings.amount)) as full_paid',
                    'schedules.refund_terms as refund_terms',
                    'SUM(bookings.is_installment) as bookings_with_installment',
                    'DATEDIFF(schedules.start_date, NOW()) as date_diff'
                ])
            )
            ->join('services', 'services.id', '=', 'schedules.service_id')
            ->join('service_types', 'service_types.id', '=', 'services.service_type_id')
            ->join('bookings', static function ($join) {
                $join->on(function ($join) {
                    $join->on('bookings.schedule_id', '=', 'schedules.id')
                        ->whereNotIn('bookings.status', ['canceled']);
                });
            })
            ->where('services.user_id', $request->user()->id)
            ->whereNotIn('services.service_type_id', ['bespoke', 'appointment'])
            ->groupBy('schedules.id')
            ->orderByRaw('ABS(date_diff)')
            ->paginate($request->getLimit());

        $schedules = $paginator->getCollection();

        return response(
            fractal($schedules, new MyClientUpcomingTransformer())->parseIncludes($request->getIncludes())
                ->toArray()
        )->withPaginationHeaders($paginator);
    }

    public function closed(Request $request)
    {
        $paginator = Booking::query()->selectRaw(
            implode(', ', [
                'schedules.id as id',
                'services.title as service_name',
                'services.id as service_id',
                'service_types.name as service_type',
                'schedules.title as schedule_name',
                'purchases.created_at as purchase_date',
                'concat(users.first_name, " ", users.last_name) as client',
                'users.id as client_id',
                'bookings.reference as reference',
                'purchases.price as paid',
                'bookings.datetime_to as closure_date',
                'bookings.datetime_from as start_date',
                'bookings.cancelled_at as cancelled_date',
                'bookings.completed_at as completed_date',
                'bookings.status as status_full',
                'bookings.id as booking_id',
                'IF (bookings.status = "completed", "Complete", "Cancel") as status',
            ])
        )->join('schedules', 'schedules.id', '=', 'bookings.schedule_id')
            ->join('services', 'services.id', '=', 'schedules.service_id')
            ->join('service_types', 'service_types.id', '=', 'services.service_type_id')
            ->join('purchases', 'purchases.id', '=', 'bookings.purchase_id')
            ->join('users', 'users.id', '=', 'bookings.user_id')
            ->where('services.user_id', $request->user()->id)
            ->whereIn('bookings.status', ['completed', 'canceled'])->paginate($request->getLimit());

        $bookings = $paginator->getCollection();

        return response(
            fractal($bookings, new MyClientClosedTransformer())->parseIncludes($request->getIncludes())
                ->toArray()
        )->withPaginationHeaders($paginator);
    }

    public function purchases(Request $request)
    {
        $paginator = Purchase::query()->selectRaw(
            implode(', ', [
                'purchases.id as id',
                'purchases.amount as amount',
                'bookings.id as booking_id',
                'bookings.reference as booking_reference',
                'services.id as service_id',
                'services.title as service_name',
                'service_types.name as service_type',
                'schedules.title as schedule_name',
                'purchases.created_at as purchase_date',
                'concat(users.first_name, " ", users.last_name) as client',
                'purchases.price as paid',
                'schedules.location_displayed as location',
                'schedules.city as city',
                'countries.nicename as country',
                'schedules.url as url',
                'schedules.refund_terms as refund_terms',
                'bookings.reference as reference',
            ])
        )->join('services', 'services.id', '=', 'purchases.service_id')
            ->join('service_types', 'service_types.id', '=', 'services.service_type_id')
            ->join('schedules', 'schedules.id', '=', 'purchases.schedule_id')
            ->leftJoin('countries', 'schedules.country_id', '=', 'countries.id')
            ->join('users', 'users.id', '=', 'purchases.user_id')
            ->join('bookings', static function ($join) {
                $join->on(function ($join) {
                    $join->on('bookings.purchase_id', '=', 'purchases.id')
                        ->whereNotIn('bookings.status', ['canceled', 'completed']);
                });
            })
            ->where('services.user_id', $request->user()->id)
            ->where('services.service_type_id', '=', 'bespoke')
            ->orderBy('id', 'desc')->paginate($request->getLimit());

        $purchases = $paginator->getCollection();

        return response(
            fractal(
                $purchases,
                new MyClientPurchaseTransformer()
            )->parseIncludes($request->getIncludes())
        )->withPaginationHeaders($paginator);
    }
}
