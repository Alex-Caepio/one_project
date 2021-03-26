<?php

namespace App\Http\Controllers;

use App\Actions\Schedule\CreateRescheduleRequestsOnScheduleUpdate;
use App\Actions\Schedule\GetAvailableAppointmentTimeOnDate;
use App\Actions\Schedule\HandlePricesUpdate;
use App\Events\ServiceScheduleCancelled;
use App\Events\ServiceScheduleLive;
use App\Http\Requests\Schedule\PurchaseScheduleRequest;
use App\Http\Requests\Schedule\GenericUpdateSchedule;
use App\Models\Price;
use App\Models\Service;
use App\Models\Schedule;
use App\Models\ScheduleUser;
use App\Models\ScheduleFreeze;
use App\Http\Requests\Request;
use App\Transformers\UserTransformer;
use App\Transformers\ScheduleTransformer;
use App\Http\Requests\Schedule\CreateScheduleInterface;
use Carbon\Carbon;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index(Service $service, Request $request)
    {
        $schedule = Schedule::selectRaw('schedules.*')
            ->where('service_id', $service->id)
            ->join('services', function($join)
            {
                $join->on('services.id', '=', 'schedules.service_id');
            })
            ->where(function($q){
                $q->where('schedules.is_published', true)
                    ->orWhere('services.user_id', Auth::id());
            })
            ->groupBy('schedules.id')
            ->get();

        return fractal($schedule, new ScheduleTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function show(Schedule $schedule, Request $request)
    {
        $schedule->with($request->getIncludes());
        return fractal($schedule, new ScheduleTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function store(CreateScheduleInterface $request, Service $service, StripeClient $stripe)
    {
        $data               = $request->all();
        $data['service_id'] = $service->id;
        $schedule           = Schedule::create($data);

        if ($request->filled('media_files')) {
            $schedule->media_files()->createMany($request->get('media_files'));
        }
        if ($request->filled('prices')) {

            $prices = $data['prices'];
            foreach  ($prices as $key => $price ){
                $stripePrice = $stripe->prices->create([
                    'unit_amount' => $prices[$key]['cost'],
                    'currency' => config('app.platform_currency'),
                    'product' => $service->stripe_id,
                ]);

                $prices[$key]['stripe_id'] = $stripePrice->id;
            }
            $schedule->prices()->createMany($prices);
        }
        if ($request->filled('schedule_availabilities')) {
            $schedule->schedule_availabilities()->createMany($request->get('schedule_availabilities'));
        }
        if ($request->filled('schedule_unavailabilities')) {
            $schedule->schedule_unavailabilities()->createMany($request->get('schedule_unavailabilities'));
        }
        if ($request->filled('schedule_files')) {
            $schedule->schedule_files()->createMany($request->get('schedule_files'));
        }
        if ($request->filled('schedule_hidden_files')) {
            $schedule->schedule_hidden_files()->createMany($request->get('schedule_hidden_files'));
        }

        return fractal($schedule, new ScheduleTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function update(GenericUpdateSchedule $request, Schedule $schedule, StripeClient $stripe)
    {
        $schedule->update($request->all());

        if ($request->has('media_files')) {
            $schedule->media_files()->delete();
            $schedule->media_files()->createMany($request->get('media_files'));
        }
        if ($request->has('prices')) {
            run_action(HandlePricesUpdate::class, $request->get('prices'), $schedule);
        }
        if ($request->filled('schedule_availabilities')) {
            $schedule->schedule_availabilities()->delete();
            $schedule->schedule_availabilities()->createMany($request->get('schedule_availabilities'));
        }
        if ($request->filled('schedule_unavailabilities')) {
            $schedule->schedule_unavailabilities()->delete();
            $schedule->schedule_unavailabilities()->createMany($request->get('schedule_unavailabilities'));
        }
        if ($request->has('schedule_files')) {
            $schedule->schedule_files()->delete();
            $schedule->schedule_files()->createMany($request->get('schedule_files'));
        }
        if ($request->has('schedule_hidden_files')) {
            $schedule->schedule_hidden_files()->delete();
            $schedule->schedule_hidden_files()->createMany($request->get('schedule_hidden_files'));
        }

        run_action(CreateRescheduleRequestsOnScheduleUpdate::class, $request, $schedule);

        return fractal($schedule, new ScheduleTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function availabilities(Schedule $schedule)
    {
        $time           = Carbon::now()->subMinutes(15);
        $amountTotal   = $schedule->attendees;
        $amountBought  = ScheduleUser::where('schedule_id', $schedule->id)->count();
        $amountFreezed = ScheduleFreeze::where('schedule_id', $schedule->id)
            ->where('freeze_at', '>', $time->toDateTimeString())->count();
        $amount_left    = $amountTotal - $amountBought - $amountFreezed;

        return response([$amountTotal, $amount_left, $amountBought, $amountFreezed]);
    }

    public function freeze(Schedule $schedule, PurchaseScheduleRequest $request)
    {
        $personalFreezed = ScheduleFreeze::where('schedule_id', $schedule->id)
            ->where('user_id', Auth::id())->first();

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

    public function allUser(Schedule $schedule)
    {
        $reschedule = $schedule->users()->get();
        return fractal($reschedule, new UserTransformer())->respond();
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return response(null, 204);
    }

    public function appointmentsOnDate(Price $price, $date) {
        return run_action(GetAvailableAppointmentTimeOnDate::class, $price, $date);
    }

    public function copy(Service $service) {
        $serviceCopy = $service->replicate();
        $serviceCopy->title = "{$service->title} (copy)";
        $serviceCopy->save();

        foreach($service->schedules as $schedule) {
            $scheduleCopy = $schedule->replicate();
            $scheduleCopy->service_id = $serviceCopy->id;
            $scheduleCopy->save();

            foreach ($schedule->prices as $price) {
                $priceCopy = $price->replicate();
                $priceCopy->schedule_id = $scheduleCopy->id;
                $priceCopy->save();
            }

            foreach($schedule->schedule_availabilities as $scheduleAvailabilitie) {
                $scheduleAvailabilitieCopy = $scheduleAvailabilitie->replicate();
                $scheduleAvailabilitieCopy->schedule_id = $scheduleCopy->id;
                $scheduleAvailabilitieCopy->save();
            }

            foreach($schedule->schedule_unavailabilities as $scheduleUnavailabilitie) {
                $scheduleUnavailabilitieCopy = $scheduleUnavailabilitie->replicate();
                $scheduleUnavailabilitieCopy->schedule_id = $scheduleCopy->id;
                $scheduleUnavailabilitieCopy->save();
            }
        }
        return response(null, 204);
    }

    public function copySchedule(Schedule $schedule) {

        $scheduleCopy = $schedule->replicate();
        $scheduleCopy->title = "{$schedule->title} (copy)";
        $scheduleCopy->save();

            foreach ($schedule->prices as $price) {
                $priceCopy = $price->replicate();
                $priceCopy->schedule_id = $scheduleCopy->id;
                $priceCopy->save();
            }

            foreach($schedule->schedule_availabilities as $scheduleAvailabilitie) {
                $scheduleAvailabilitieCopy = $scheduleAvailabilitie->replicate();
                $scheduleAvailabilitieCopy->schedule_id = $scheduleCopy->id;
                $scheduleAvailabilitieCopy->save();
            }

            foreach($schedule->schedule_unavailabilities as $scheduleUnavailabilitie) {
                $scheduleUnavailabilitieCopy = $scheduleUnavailabilitie->replicate();
                $scheduleUnavailabilitieCopy->schedule_id = $scheduleCopy->id;
                $scheduleUnavailabilitieCopy->save();
            }

        return response(null, 204);
    }
}
