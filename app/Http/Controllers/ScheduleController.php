<?php

namespace App\Http\Controllers;

use App\Actions\Schedule\CreateRescheduleRequestsOnScheduleUpdate;
use App\Actions\Schedule\GetAvailableAppointmentTimeOnDate;
use App\Actions\Schedule\HandlePricesUpdate;
use App\Actions\Schedule\ScheduleStore;
use App\Actions\Schedule\ScheduleUpdate;
use App\Events\ServiceScheduleCancelled;
use App\Events\ServiceScheduleLive;
use App\Helpers\UserRightsHelper;
use App\Http\Requests\InstallmentCalendar;
use App\Http\Requests\Schedule\PurchaseScheduleRequest;
use App\Http\Requests\Schedule\GenericUpdateSchedule;
use App\Http\Requests\Schedule\ScheduleOwnerRequest;
use App\Models\Booking;
use App\Models\Plan;
use App\Models\Price;
use App\Models\Service;
use App\Models\Schedule;
use App\Models\ScheduleUser;
use App\Models\ScheduleFreeze;
use App\Http\Requests\Request;
use App\Models\User;
use App\Transformers\BookingTransformer;
use App\Transformers\ServiceTransformer;
use App\Transformers\UserTransformer;
use App\Transformers\ScheduleTransformer;
use App\Http\Requests\Schedule\CreateScheduleInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{

    public function index(Service $service, Request $request)
    {
        $scheduleQuery = Schedule::where('service_id', $service->id)->with('service');

        $scheduleQuery->where('schedules.is_published', true)->where(
            function ($q) {
                $q->where('schedules.start_date', '>=', now())->orWhereNull('schedules.start_date');
            }
        );

        $scheduleQuery->with($request->getIncludes())->selectRaw('*, DATEDIFF(start_date, NOW()) as date_diff')
            ->orderByRaw('ABS(date_diff)');

        $schedule = $scheduleQuery->get();

        return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())->toArray();
    }


    public function ownerScheduleList(Service $service, Request $request)
    {
        $scheduleQuery = Schedule::where('service_id', $service->id);

        $scheduleQuery->whereHas(
            'service',
            static function ($query) {
                $query->where('user_id', Auth::id());
            }
        );

        $schedule = $scheduleQuery->get();

        return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function rescheduleScheduleList(Schedule $schedule, Request $request)
    {
        $scheduleQuery = Schedule::where('service_id', $schedule->service_id)->where('id', '<>', $schedule->id)->where(
            'is_published',
            true
        );

        $requestIncludes = $request->getIncludes();

        $scheduleQuery->where(
            function ($q) {
                $q->where('schedules.start_date', '>=', now())->orWhereNull('schedules.start_date');
            }
        );

        // price option for client
        if (Auth::user()->account_type === User::ACCOUNT_CLIENT && $request->filled('booking_id')) {
            $booking = Booking::with('price')->where('id', (int)$request->get('booking_id'))->where(
                'schedule_id',
                $schedule->id
            )->first();
            if (!$booking) {
                return response('Booking not found', 500);
            }

            $scheduleQuery->whereHas(
                'prices',
                static function ($query) use ($booking) {
                    $query->where('prices.cost', '<=', $booking->price->cost)->orWhere('is_free', true);
                }
            );

            // attendees filtrator
            $scheduleCollection = $scheduleQuery->get()->filter(
                static function ($item) {
                    return $item->attendees === null ||
                        (int)$item->attendees > Booking::where('schedule_id', $item->id)->uncanceled()->sum(
                            'amount'
                        );
                }
            );
        } else {
            $scheduleCollection = $scheduleQuery->get();
        }

        return fractal($scheduleCollection, new ScheduleTransformer())->parseIncludes($requestIncludes)->toArray();
    }

    public function show(Schedule $schedule, Request $request)
    {
        $schedule->with($request->getIncludes());

        return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function store(CreateScheduleInterface $request, Service $service)
    {
        $schedule = run_action(ScheduleStore::class, $request, $service);

        return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function update(CreateScheduleInterface $request, Schedule $schedule)
    {
        $schedule = run_action(ScheduleUpdate::class, $request, $schedule);

        return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function availabilities(Schedule $schedule)
    {
        $time = Carbon::now()->subMinutes(15);
        $amountTotal = (int)$schedule->attendees;
        $amountBought = Booking::where('schedule_id', $schedule->id)->uncanceled()->sum('amount');
        $amountFreezed = ScheduleFreeze::query()
            ->where('schedule_id', $schedule->id)
            ->where('freeze_at', '>', $time->toDateTimeString())
            ->count();
        $amount_left = $amountTotal - $amountBought - $amountFreezed;

        return response([$amountTotal, $amount_left, $amountBought, $amountFreezed]);
    }

    public function freeze(Schedule $schedule, PurchaseScheduleRequest $request)
    {
        $personalFreezed = ScheduleFreeze::where('schedule_id', $schedule->id)->where('user_id', Auth::id())->first();

        if ($personalFreezed === null) {
            if ($schedule->service->service_type_id !== 'appointment') {
                $this->createNonAppointmentFreeze($request, $schedule);
            } else {
                $this->createAppointmentFreeze($request, $schedule);
            }
        }
    }

    public function allUser(Schedule $schedule)
    {
        $reschedule = $schedule->users()->get();

        return fractal($reschedule, new UserTransformer())->respond();
    }

    public function allBookings(Schedule $schedule, Request $request)
    {
        $bookingQuery = $schedule->bookings();
        $isActive = $request->getBoolFromRequest('is_active');
        if ($isActive === true) {
            $bookingQuery->active();
        }
        $bookingQuery->with($request->getIncludes());
        $bookingQuery->with(['purchase','purchase.instalments']);
        $paginator = $bookingQuery->paginate($request->getLimit());
        $fractal =
            fractal($paginator->getCollection(), new BookingTransformer())
                ->parseIncludes($request->getIncludes())
                ->toArray();

        return response($fractal)->withPaginationHeaders($paginator);
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return response(null, 204);
    }

    public function appointmentsOnDate(Price $price, $date)
    {
        return run_action(GetAvailableAppointmentTimeOnDate::class, $price, $date);
    }

    public function copy(Schedule $schedule)
    {
        if (UserRightsHelper::userAllowAddSchedule(Auth::user(), $schedule->service)) {
            $scheduleCopy = $schedule->replicate();
            $scheduleCopy->title = "{$schedule->title} (copy)";
            $scheduleCopy->is_published = false;
            $scheduleCopy->save();

            foreach ($schedule->prices as $price) {
                $priceCopy = $price->replicate();
                $priceCopy->schedule_id = $scheduleCopy->id;
                $priceCopy->save();
            }

            foreach ($schedule->schedule_availabilities as $scheduleAvailabilitie) {
                $scheduleAvailabilitieCopy = $scheduleAvailabilitie->replicate();
                $scheduleAvailabilitieCopy->schedule_id = $scheduleCopy->id;
                $scheduleAvailabilitieCopy->save();
            }

            foreach ($schedule->schedule_unavailabilities as $scheduleUnavailabilitie) {
                $scheduleUnavailabilitieCopy = $scheduleUnavailabilitie->replicate();
                $scheduleUnavailabilitieCopy->schedule_id = $scheduleCopy->id;
                $scheduleUnavailabilitieCopy->save();
            }
        } else {
            return response(
                ['message' => 'Sorry, you have reached the maximum allowed schedules for your subscription plan'],
                422
            );
        }

        return response(null, 204);
    }

    public function availableInstalments(Schedule $schedule): array
    {
        return $schedule->isInstallmentAvailable() ? $schedule->getInstallmentPeriods() : [];
    }

    public function availableInstalmentsDates(Schedule $schedule, InstallmentCalendar $request)
    {
        $periods = $schedule->getInstallmentPeriods();
        $targetPeriod = (int)$request->period;
        if (in_array($targetPeriod, $periods, true) && $schedule->isInstallmentAvailable()) {
            $price = $schedule->prices()->where('id', $request->price_id)->first();
            if (!$price) {
                abort(404, 'Price was not found');
            }
            $total = $price->cost * $request->amount;

            return response($schedule->calculateInstallmentsCalendar($total, $targetPeriod));
        }

        return response([]);
    }


    public function publish(ScheduleOwnerRequest $request, Schedule $schedule)
    {
        $schedule->is_published = true;
        $schedule->save();

        return response(null, 204);
    }

    public function unpublish(ScheduleOwnerRequest $request, Schedule $schedule)
    {
        $schedule->is_published = false;
        $schedule->save();

        return response(null, 204);
    }

    private function createAppointmentFreeze($request, Schedule $schedule)
    {
        $time = Carbon::now();
        foreach ($request->get('availabilities') as $availability) {
            $freeze = new ScheduleFreeze();
            $start = Carbon::parse($availability['datetime_from'])->setTimezone('UTC');
            $freeze->forceFill(
                [
                    'start_at' => $start,
                    'freeze_at' => $time,
                    'user_id' => Auth::id(),
                    'practitioner_id' => $schedule->service->user_id,
                    'schedule_id' => $schedule->id,
                    'quantity' => 1,
                    'price_id' => $request->price_id
                ]
            );
            $freeze->save();
        }
    }

    private function createNonAppointmentFreeze(PurchaseScheduleRequest $request, Schedule $schedule)
    {
        $freeze = new ScheduleFreeze();
        $freeze->forceFill(
            [
                'start_at' => $schedule->start_date,
                'freeze_at' => Carbon::now(),
                'user_id' => Auth::id(),
                'practitioner_id' => $schedule->service->user_id,
                'schedule_id' => $schedule->id,
                'quantity' => $request->get('amount', 1),
                'price_id' => $request->price_id
            ]
        );
        $freeze->save();
    }


}
