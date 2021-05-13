<?php

namespace App\Http\Controllers;

use App\Actions\Schedule\CreateRescheduleRequestsOnScheduleUpdate;
use App\Actions\Schedule\GetAvailableAppointmentTimeOnDate;
use App\Actions\Schedule\HandlePricesUpdate;
use App\Actions\Schedule\ScheduleStore;
use App\Actions\Schedule\ScheduleUpdate;
use App\Events\ServiceScheduleCancelled;
use App\Events\ServiceScheduleLive;
use App\Http\Requests\Schedule\PurchaseScheduleRequest;
use App\Http\Requests\Schedule\GenericUpdateSchedule;
use App\Http\Requests\Schedule\ScheduleOwnerRequest;
use App\Models\Booking;
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

class ScheduleController extends Controller {

    public function index(Service $service, Request $request) {
        $scheduleQuery = Schedule::where('service_id', $service->id);

        $scheduleQuery->where('schedules.is_published', true)->where(function($q) {
            $q->where('schedules.start_date', '>=', now())->orWhereNull('schedules.start_date');
        });

        $schedule = $scheduleQuery->get();

        return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function ownerScheduleList(Service $service, Request $request) {

        $scheduleQuery = Schedule::where('service_id', $service->id);

        $scheduleQuery->whereHas('service', static function($query) {
            $query->where('user_id', Auth::id());
        });

        $schedule = $scheduleQuery->get();

        return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function rescheduleScheduleList(Schedule $schedule, Request $request) {
        Log::info('Schedule Requested to Reschedule: '.$schedule->id);
        $scheduleQuery = Schedule::where('service_id', $schedule->service_id)->where('id', '<>', $schedule->id)
                                                                              ->where('is_published', true);

        $requestIncludes = $request->getIncludes();

        // price option for client
        if (Auth::user()->account_type !== User::ACCOUNT_CLIENT && $request->filled('booking_id')) {
            Log::info('Find with bookingID: '.$request->get('booking_id'));
            $booking = Booking::with('price')
                              ->where('id', (int)$request->get('booking_id'))
                              ->where('schedule_id', $schedule->id)
                              ->first();
            if (!$booking) {
                return response('Booking not found', 500);
            }
            Log::info('Cost: '.$booking->price->cost);
            $scheduleQuery->whereHas('prices', static function($query) use($booking) {
                $query->where('prices.cost', '<=', $booking->price->cost);
            });
        }
        $scheduleQuery->where(function($q) {
            $q->where('schedules.start_date', '>=', now())->orWhereNull('schedules.start_date');
        });
        $scheduleCollection = $scheduleQuery->get();
        Log::info('Found schedules: '.$scheduleCollection->count());
        Log::info($scheduleCollection->pluck('id'));
        return fractal($scheduleCollection, new ScheduleTransformer())->parseIncludes($requestIncludes)->toArray();
    }

    public function show(Schedule $schedule, Request $request) {
        $schedule->with($request->getIncludes());
        return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function store(CreateScheduleInterface $request, Service $service) {
        $schedule = run_action(ScheduleStore::class, $request, $service);

        return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function update(GenericUpdateSchedule $request, Schedule $schedule) {
        $schedule = run_action(ScheduleUpdate::class, $request, $schedule);

        return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function availabilities(Schedule $schedule) {
        $time = Carbon::now()->subMinutes(15);
        $amountTotal = $schedule->attendees;
        $amountBought = ScheduleUser::where('schedule_id', $schedule->id)->count();
        $amountFreezed =
            ScheduleFreeze::where('schedule_id', $schedule->id)->where('freeze_at', '>', $time->toDateTimeString())
                          ->count();
        $amount_left = $amountTotal - $amountBought - $amountFreezed;

        return response([$amountTotal, $amount_left, $amountBought, $amountFreezed]);
    }

    public function freeze(Schedule $schedule, PurchaseScheduleRequest $request) {
        $personalFreezed = ScheduleFreeze::where('schedule_id', $schedule->id)->where('user_id', Auth::id())->first();

        $time = Carbon::now();
        if ($personalFreezed === null) {
            $freeze = new ScheduleFreeze();
            $freeze->forceFill([
                                   'freeze_at'   => $time,
                                   'user_id'     => Auth::id(),
                                   'schedule_id' => $schedule->id,
                                   'quantity'    => $request->get('amount') ?? 1,
                                   'price_id'    => $request->price_id
                               ]);
            $freeze->save();
        }
    }

    public function allUser(Schedule $schedule) {
        $reschedule = $schedule->users()->get();
        return fractal($reschedule, new UserTransformer())->respond();
    }

    public function allBookings(Schedule $schedule, Request $request) {
        $query = $schedule->bookings()->with($request->getIncludes());
        $paginator = $query->paginate($request->getLimit());
        $fractal =
            fractal($paginator->getCollection(), new BookingTransformer())->parseIncludes($request->getIncludes())
                                                                          ->toArray();
        return response($fractal)->withPaginationHeaders($paginator);
    }

    public function destroy(Schedule $schedule) {
        $schedule->delete();
        return response(null, 204);
    }

    public function appointmentsOnDate(Price $price, $date) {
        return run_action(GetAvailableAppointmentTimeOnDate::class, $price, $date);
    }

    public function copy(Schedule $schedule) {

        $plan = Auth::user()->plan;
        $service = $schedule->service;

        if ($plan->schedules_per_service_unlimited || $plan->schedules_per_service > $service->schedules()->count()) {

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
            return response(['message' => 'The maximum allowed number of shedules per service has been exceeded.'],
                            422);
        }

        return response(null, 204);
    }

    public function availableInstalments(Schedule $schedule) {
        $dateNow = Carbon::now();
        $dateFinal = Carbon::parse($schedule->deposit_final_date);

        //can't put installments on expired schedules
        if ($dateNow->isAfter($dateFinal)) {
            return [];
        }

        $daysDiff = $dateNow->diffInDays($dateFinal);
        $periods = (int)($daysDiff / 14);
        $date = [];

        for ($i = 1; $i <= $periods; $i++) {
            $date[] += $i;
        }

        return $date;
    }
}
