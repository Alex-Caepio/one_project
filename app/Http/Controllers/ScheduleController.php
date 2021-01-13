<?php

namespace App\Http\Controllers;

use App\Actions\Schedule\CreateRescheduleRequestsOnScheduleUpdate;
use App\Actions\Schedule\HandlePricesUpdate;
use App\Events\ServiceScheduleWentLive;
use App\Models\Service;
use App\Models\Schedule;
use App\Models\ScheduleUser;
use App\Models\ScheduleFreeze;
use App\Http\Requests\Request;
use App\Transformers\UserTransformer;
//use App\Events\ServiceScheduleLive;
use App\Transformers\ScheduleTransformer;
use App\Http\Requests\Schedule\CreateScheduleInterface;
use Carbon\Carbon;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index(Service $service, Request $request)
    {
        $schedule = Schedule::all()->where('service_id', $service->id);
        return fractal($schedule, new ScheduleTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function store(CreateScheduleInterface $request, Service $service, StripeClient $stripe)
    {
        $data               = $request->all();
        $data['service_id'] = $service->id;
        $schedule           = Schedule::create($data);
        $user               = $request->user();

        if ($request->filled('media_files')) {
            $schedule->media_files()->createMany($request->get('media_files'));
        }
        if ($request->filled('prices')) {

            $prices = $request->get('prices');
            foreach  ($prices as $key => $price ){
                $stripePrice = $stripe->prices->create([
                    'unit_amount' => $prices[$key]['cost'],
                    'currency' => 'usd',
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

        // @todo fix with the new event
        //event(new ServiceScheduleLive($service, $user, $schedule));

        return fractal($schedule, new ScheduleTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function update(Request $request, Schedule $schedule, StripeClient $stripe)
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

        event(new ServiceScheduleWentLive($schedule->service, $request->user(), $schedule));

        return fractal($schedule, new ScheduleTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function availabilities(Schedule $schedule)
    {
        $time           = Carbon::now()->subMinutes(15);
        $amount_total   = $schedule->attendees;
        $amount_bought  = ScheduleUser::where('schedule_id', $schedule->id)->count();
        $amount_freezed = ScheduleFreeze::where('schedule_id', $schedule->id)
            ->where('freeze_at', '>', $time->toDateTimeString())->count();
        $amount_left    = $amount_total - $amount_bought - $amount_freezed;

        return response([$amount_total, $amount_left, $amount_bought, $amount_freezed]);
    }

    public function freeze(Schedule $schedule)
    {
        $personalFreezed = ScheduleFreeze::where('schedule_id', $schedule->id)
            ->where('user_id', Auth::id())->first();

        $time = Carbon::now();
        if ($personalFreezed === null) {
            $freeze = new ScheduleFreeze();
            $freeze->forceFill([
                'schedule_id' => $schedule->id,
                'user_id'     => Auth::id(),
                'freeze_at'   => $time
            ]);
            $freeze->save();
        }
    }

    public function allUser(Schedule $schedule)
    {
        $reschedule = $schedule->users()->get();
        return fractal($reschedule, new UserTransformer())->respond();
    }

}
