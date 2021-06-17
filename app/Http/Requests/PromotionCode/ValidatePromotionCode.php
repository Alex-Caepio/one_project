<?php


namespace App\Http\Requests\PromotionCode;

use App\Models\Promotion;
use App\Models\PromotionCode;
use App\Models\Purchase;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class ValidatePromotionCode {

    /**
     * @param \Illuminate\Validation\Validator $validator
     * @param string $promocodeName
     * @param \App\Models\Service $service
     * @param \App\Models\Schedule|null $schedule
     * @param int $totalCost
     */
    public static function validate(Validator $validator, string $promocodeName, Service $service,
                                    ?Schedule $schedule, $totalCost = 0): void {
        if (!$promoCode = PromotionCode::where('name', $promocodeName)->whereHas('promotion', function($query) {
            $query->where('status', Promotion::STATUS_ACTIVE);
        })->where('status', PromotionCode::STATUS_ACTIVE)->with(['promotion'])->first()) {
            $validator->errors()->add('promo_code', 'The Promotion code is invalid');
            return;
        }

        //initialise variables
        $promotion = $promoCode->promotion;

        $eligibleUsers = $promoCode->users()->pluck('users.id');

        $promoDisciplines = $promoCode->promotion->disciplines()->pluck('disciplines.id')->toArray();
        $promoFocusAreas = $promoCode->promotion->focus_areas()->pluck('focus_areas.id')->toArray();
        $promoServiceTypes = $promoCode->promotion->service_types()->pluck('service_types.id')->toArray();
        $promoPractitioners = $promoCode->promotion->practitioners()->pluck('users.id')->toArray();


        $serviceDisciplines = $service->disciplines()->pluck('disciplines.id')->toArray();
        $serviceFocusAreas = $service->focus_areas()->pluck('focus_areas.id')->toArray();

        if ($eligibleUsers->count() && !$eligibleUsers->has(Auth::id())) {
            $validator->errors()->add('promo_code', 'Promo code user is invalid');
        }

        //Uses Per Code
        if ($promoCode->uses_per_code > 0 &&
            Purchase::where('promocode_id', $promoCode->id)->count() >= (int)$promoCode->uses_per_code) {
            $validator->errors()->add('promo_code', 'Sorry, this code has already been used');
        }

        //Uses Per Client
        if ($promoCode->uses_per_client > 0 &&
            Purchase::where('promocode_id', $promoCode->id)->where('user_id', Auth::id())->count() >=
            (int)$promoCode->uses_per_client) {
            $validator->errors()->add('promo_code', 'You are not allowed to use this promocode');
        }

        //checks below
        if (!empty($promotion->spend_min) && $totalCost < $promotion->spend_min) {
            $validator->errors()->add('promo_code',
                                      'Sorry, this Service is under the minimum cost allowed');
        }

        if (!empty($promotion->spend_max) && $totalCost > $promotion->spend_max) {
            $validator->errors()->add('promo_code',
                                      'Sorry, this Service is above the maximum cost allowed');
        }

        if ($schedule instanceof Schedule) {
            if ($promotion->valid_from && Carbon::parse($promotion->valid_from) > Carbon::parse($schedule->start_date)) {
                $validator->errors()->add('promo_code',
                                          'Sorry, this code is not valid for the date of Booking');
            }

            if ($promotion->expiry_date && Carbon::parse($promotion->expiry_date) < Carbon::parse($schedule->end_date)) {
                $validator->errors()->add('promo_code', "Sorry, this code is not valid for the date of Booking");
            }
        }

        if (count($promoDisciplines) && !count(array_intersect($promoDisciplines, $serviceDisciplines))) {
            $validator->errors()->add('promo_code', 'You are not allowed to use the promocode with this discipline');
        } elseif (count($promoFocusAreas) && !count(array_intersect($promoFocusAreas, $serviceFocusAreas))) {
            $validator->errors()->add('promo_code', 'You are not allowed to use the promocode with this focus area');
        } elseif (count($promoServiceTypes) && !in_array($service->service_type_id, $promoServiceTypes)) {
            $validator->errors()->add('promo_code', 'Sorry, this code cannot be used on this service');
        } elseif (count($promoPractitioners) && !in_array($service->user_id, $promoPractitioners)) {
            $validator->errors()->add('promo_code', 'Sorry, this code cannot be used with this practitioner');
        }

    }
}
