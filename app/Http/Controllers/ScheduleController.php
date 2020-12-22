<?php

namespace App\Http\Controllers;

use App\Http\Requests\Schedule\PurchaseScheduleRequest;
use App\Models\Booking;
use App\Models\Price;
use App\Models\RescheduleRequest;
use App\Models\Service;
use App\Models\Schedule;
use App\Models\ScheduleUser;
use App\Models\PromotionCode;
use App\Models\ScheduleFreeze;
use App\Http\Requests\Request;
use App\Models\UsedPromotionCode;
use App\Transformers\UserTransformer;
//use App\Events\ServiceScheduleLive;
use App\Transformers\ScheduleTransformer;
use App\Actions\Promo\CalculatePromoPrice;
use App\Http\Requests\PromotionCode\PurchaseRequest;
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

    public function store(CreateScheduleInterface $request, Service $service)
    {
        $data               = $request->all();
        $data['service_id'] = $service->id;
        $schedule           = Schedule::create($data);
        $user               = $request->user();

        if ($request->filled('media_files')) {
            $schedule->media_files()->createMany($request->get('media_files'));
        }
        if ($request->filled('prices')) {
            $schedule->prices()->createMany($request->get('prices'));
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

    public function update(Request $request, Service $service, Schedule $schedule)
    {
        $schedule->update($request->all());

        if ($request->has('media_files')) {
            $schedule->media_files()->delete();
            $schedule->media_files()->createMany($request->get('media_files'));
        }
        if ($request->has('prices')) {
            $schedule->prices()->delete();
            $schedule->prices()->createMany($request->get('prices'));
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

        /* The whole if block should be taken out to some other class */
        if ($this->requiresReschedule($request, $schedule)) {
            //should be moved to constant
            $bookings = $schedule->service->service_type == 'appointment'
                ? $schedule->getOutsiderBookings()
                : Booking::where('schedule_id', $schedule->id)->get();

            //In order to avoid duplicated reschedule request we have to delete all prevous first
            $schedule->rescheduleRequests()->whereIn('booking_id', $bookings->pluck('id'))->delete();

            $rescheduleRequests = [];
            foreach ($bookings as $booking) {
                $rescheduleRequests[] = [
                    'user_id'         => $booking->user_id,
                    'booking_id'      => $booking->id,
                    'schedule_id'     => $booking->schedule_id,
                    'new_schedule_id' => $schedule->id,
                    'created_at'      => Carbon::now()->format('Y-m-d H:i:s')
                ];
            }

            if ($this->locationHasChanged($request, $schedule)) {
                foreach ($rescheduleRequests as $key => $reschedule) {
                    $rescheduleRequests[$key]['old_location_displayed'] = $schedule->location_displayed;
                    $rescheduleRequests[$key]['new_location_displayed'] = $request->get('location_displayed');
                }
            }

            if ($this->dateHasChanged($request, $schedule)) {
                foreach ($rescheduleRequests as $key => $reschedule) {
                    $rescheduleRequests[$key]['old_start_date'] = $schedule->start_date;
                    $rescheduleRequests[$key]['new_start_date'] = $request->get('start_date');
                    $rescheduleRequests[$key]['old_end_date']   = $schedule->end_date;
                    $rescheduleRequests[$key]['new_end_date']   = $request->get('end_date');
                }
            }

            RescheduleRequest::insert($rescheduleRequests);
        }

        // @todo replace with the new event
        // event(new ServiceScheduleLive($service, $user, $schedule));
        // event(new ServiceScheduleWentLive($service, $request->user(), $schedule));

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

    public function purchase(PurchaseScheduleRequest $request, Schedule $schedule)
    {
        $price = $schedule->prices()->find($request->get('price_id'));
        $cost  = $price->cost;

        if ($request->has('promo_code')) {
            $promo = PromotionCode::where('name', $request->get('promo_code'))->first();
            $cost  = run_action(CalculatePromoPrice::class, $promo, $schedule->cost);
        }

        if ($schedule->service->service_type_id == 'appointment') {
            $availabilities = $request->get('availabilities');
            foreach ($availabilities as $availability) {
                $booking                  = new Booking();
                $booking->user_id         = $request->user()->id;
                $booking->price_id        = $request->get('price_id');
                $booking->schedule_id     = $schedule->id;
                $booking->availability_id = $availability['availability_id'];
                $booking->datetime_from   = $availability['datetime_from'];
                $datetimeTo               = (new Carbon($booking->datetime_from))->addMinutes($price->duration);
                $booking->datetime_to     = $datetimeTo->format('Y-m-d H:i:s');
                $booking->cost            = $cost;
                $booking->save();
            }
        } else {
            $booking              = new Booking();
            $booking->user_id     = $request->user()->id;
            $booking->price_id    = $request->get('price_id');
            $booking->schedule_id = $schedule->id;
            $booking->cost        = $cost;
            $booking->save();
        }
        ScheduleFreeze::where('schedule_id', $schedule->id)
            ->where('user_id', $request->user()->id)
            ->delete();

        if ($request->has('promo_code')) {
            $userPromoCode = new UsedPromotionCode();
            $userPromoCode->forceFill(
                [
                    'user_id'           => $request->user()->id,
                    'schedule_id'       => $schedule->id,
                    'promotion_code_id' => $promo->id,
                ]
            );
            $userPromoCode->save();
        }

        return response(null, 200);


//        $name         = $request->get('promo_code');
//        $scheduleCost = $schedule->cost;
//        $user         = Auth::user();
//        $promo        = PromotionCode::where('name', $name)->first();
//
//        run_action(CalculatePromoPrice::class, $promo, $scheduleCost);
//
//        $user->getCommission();

//                $userPromoCode = new UsedPromotionCode();
//                $userPromoCode->forceFill(
//                    [
//                        'user_id' => $user,
//                        'schedule_id' => $schedule->id,
//                        'promotion_code_id' => $promo->id,
//                    ]
//                );
//                $userPromoCode->save();
//        if ($schedule->isSoldOut()){
//            $stripe->charges->create([
//                'amount' => $newSchedule,
//                'currency' => 'usd',
//                'customer' => $user->stripe_id,
//                'description' => 'My First Test Charge (created for API docs)',
//            ]);
//            $schedule->users()->save($user);
//        }
    }

    public function promoCode(Schedule $schedule, PurchaseRequest $request)
    {
        $name         = $request->get('promo_code');
        $scheduleCost = $schedule->cost;
        $promo        = PromotionCode::where('name', $name)->first();
        run_action(CalculatePromoPrice::class, $promo, $scheduleCost);

        return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    protected function requiresReschedule(Request $request, Schedule $schedule): bool
    {
        return $this->dateHasChanged($request, $schedule)
            || $this->locationHasChanged($request, $schedule)
            || $request->filled('schedule_unavailabilities')
            || $request->filled('schedule_availabilities');
    }

    protected function dateHasChanged(Request $request, Schedule $schedule): bool
    {
        if ($request['start_date'] != $schedule->start_date) {
            return true;
        } else if ($request['end_date'] != $schedule->end_date) {
            return true;
        }

        return false;
    }

    protected function locationHasChanged(Request $request, Schedule $schedule): bool
    {
        if ($request['location_id'] != $schedule->location_id) {
            return true;
        } else if ($request['venue'] != $schedule->venue) {
            return true;
        } else if ($request['city'] != $schedule->city) {
            return true;
        } else if ($request['country'] != $schedule->country) {
            return true;
        } else if ($request['post_code'] != $schedule->post_code) {
            return true;
        } else if ($request['location_displayed'] != $schedule->location_displayed) {
            return true;
        } else if ($request['is_virtual'] != $schedule->is_virtual) {
            return true;
        }

        return false;
    }
}
