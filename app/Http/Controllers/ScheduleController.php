<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Schedule;
use App\Models\ScheduleUser;
use App\Models\PromotionCode;
use App\Models\ScheduleFreeze;
use App\Http\Requests\Request;
use App\Transformers\UserTransformer;
use App\Actions\Schedule\ScheduleStore;
use App\Events\ServiceScheduleWentLive;
use App\Transformers\ScheduleTransformer;
use App\Actions\Promo\CalculatePromoPrice;
use App\Http\Requests\PromotionCode\PurchaseRequest;
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

    public function store(Request $request, Service $service)
    {
        $schedule = run_action(ScheduleStore::class, $request, $service);
        $user     = $request->user();
        event(new ServiceScheduleWentLive($service, $user, $schedule));
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
        if ($personalFreezed == null) {
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

    public function purchase(Schedule $schedule, StripeClient $stripe, Request $request)
    {
        $name         = $request->get('promo_code');
        $scheduleCost = $schedule->cost;
        $user         = Auth::user();
        $promo        = PromotionCode::where('name', $name)->first();

        run_action(CalculatePromoPrice::class, $promo, $scheduleCost);

        $user->getCommission();

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
}
