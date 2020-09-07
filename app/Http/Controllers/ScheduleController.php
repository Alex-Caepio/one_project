<?php

namespace App\Http\Controllers;

use App\Actions\Schedule\ScheduleStore;
use App\Http\Requests\Request;
use App\Models\PromotionCode;
use App\Models\Schedule;
use App\Models\ScheduleFreeze;
use App\Models\ScheduleUser;
use App\Models\Service;
use App\Transformers\ScheduleTransformer;
use App\Transformers\UserTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class ScheduleController extends Controller
{

    public function index(Service $service, Request $request)
    {
        $schedule = Schedule::all()->where('service_id', $service->id);
        return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function store(Request $request, Service $service)
    {
        run_action(ScheduleStore::class, $request, $service);
    }

    public function availabilities(Schedule $schedule)
    {
        $time = Carbon::now()->subMinutes(15);
        $amount_total = $schedule->attendees;
        $amount_bought = ScheduleUser::where('schedule_id', $schedule->id)->count();
        $amount_freezed = $freezed = ScheduleFreeze::where('schedule_id', $schedule->id)
            ->where('freeze_at', '>', $time->toDateTimeString())->count();
        $amoint_left = $amount_total - $amount_bought - $amount_freezed;
        return response([$amount_total, $amoint_left, $amount_bought, $amount_freezed]);
    }

    public function freeze(Schedule $schedule)
    {
        $personalFreezed = ScheduleFreeze::where('schedule_id', $schedule->id)
            ->where('user_id', Auth::id())->first();
        $time = Carbon::now();
        if ($personalFreezed == null) {
            $freeze = new ScheduleFreeze();
            $freeze->forceFill([
                'schedule_id' => $schedule->id,
                'user_id' => Auth::id(),
                'freeze_at' => $time
            ]);
            $freeze->save();
        }
    }

    public function allUser(Schedule $schedule)
    {
        $reschedule = $schedule->users()->get();
        return fractal($reschedule, new UserTransformer())->respond();
    }

    public function purchase(Schedule $schedule, StripeClient $stripe, Request $request)
    {
        $name = $request->get('promo_code');
//        $scheduleCost = $schedule->cost;
        $user = Auth::user();
        $promo = PromotionCode::where('name', $name)->first();
       // $promotion = $promo->promotion;
//        $promoDiscipline = $promotion->discipline_id;
//        $promoServiceType = $promotion->service_type_id;
//        $promoFocusArea = $promotion->focus_area_id;
//        $serviceId = $schedule->service->id;
//        $service = Service::all()->where('id', $serviceId)->first();
//        $serviceDiscipline = $service->disciplines()->first()->id;
//        $serviceServiceType = $service->service_types()->first()->id;
//        $serviceFocusArea = $service->focus_areas()->first()->id;
//        $promoUser = $promo->has('users');
//        if ($promoUser) {
//            $promoUserId = $promo->users()->where('users.id', $request->user()->id)->exists();
//            if (!$promoUserId) {
//                return response(null, 204);
//            }
//        }
//        if ($promoDiscipline == $serviceDiscipline && $promoServiceType == $serviceServiceType && $promoFocusArea == $serviceFocusArea) {
//            if (!$name == null) {
//                $percentage = $promotion->where('discount_type', 'percentage');
//                $promoValue = $promotion->discount_value;
//                if ($promotion->valid_from >= $schedule->start_date && $promotion->expiry_date <= $schedule->end_date) {
//                    if ($scheduleCost >= $promotion->spend_min || $promotion->spend_min == null) {
//                        if ($scheduleCost <= $promotion->spend_max || $promotion->spend_max == null) {
//                            if ($percentage) {
//                                $newSchedule = $scheduleCost - ($scheduleCost * ($promoValue / 100));
//                                $schedule->cost = $newSchedule;
//                            } else {
//                                $newSchedule = $scheduleCost - $promoValue;
//                                $schedule->cost = $newSchedule;
//                            }
//                        }
//                    }
//                }
//                $userPromoCode = new UsedPromotionCode();
//                $userPromoCode->forceFill(
//                    [
//                        'user_id' => $scheduleUserId,
//                        'schedule_id' => $schedule->id,
//                        'promotion_code_id' => $promo->id,
//                    ]
//                );
//                $userPromoCode->save();
//            }
        if ($schedule->soldOut()){
            $stripe->charges->create([
                'amount' => $schedule->cost,
                'currency' => 'usd',
                'customer' => $user->stripe_id,
                'description' => 'My First Test Charge (created for API docs)',
            ]);
            $schedule->users()->save($user);
        }
        // }
    }

    public function promoCode(Schedule $schedule, Request $request)
    {
        $name = $request->get('promo_code');
        $promo = PromotionCode::query()->where('name', $name)->first();
        $percentage = $promo->where('discount_type', 'percentage');
        $scheduleCost = $schedule->cost;
        $promoValue = $promo->discount_value;
        $promoDiscipline = $promo->discipline_id;
        $promoServiceType = $promo->service_type_id;
        $promoFocusArea = $promo->focus_area_id;
        $serviceId = $schedule->service->id;
        $service = Service::all()->where('id', $serviceId)->first();
        $serviceDiscipline = $service->disciplines()->first()->id;
        $serviceServiceType = $service->service_types()->first()->id;
        $serviceFocusArea = $service->focus_areas()->first()->id;
        $promoUser = $promo->has('users');
        if ($promoUser) {
            $promoUserId = $promo->users()->where('users.id', $request->user()->id)->exists();
            if (!$promoUserId) {
                return response(null, 204);
            }
        }
        if ($promoDiscipline == $serviceDiscipline && $promoServiceType == $serviceServiceType && $promoFocusArea == $serviceFocusArea) {
            if (!$name == null) {
                if ($promo->valid_from >= $schedule->start_date && $promo->expiry_date <= $schedule->end_date) {
                    if ($scheduleCost >= $promo->spend_min || $promo->spend_min == null) {
                        if ($scheduleCost <= $promo->spend_max || $promo->spend_max == null) {
                            if ($percentage) {
                                $newSchedule = $scheduleCost - ($scheduleCost * ($promoValue / 100));
                                $schedule->cost = $newSchedule;
                            } else {
                                $newSchedule = $scheduleCost - $promoValue;
                                $schedule->cost = $newSchedule;
                            }
                            return fractal($schedule, new ScheduleTransformer())->parseIncludes($request->getIncludes())
                                ->toArray();
                        }
                    }
                }
            }
        }
        return response(null, 204);
    }

}
