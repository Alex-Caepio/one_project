<?php


namespace App\Http\Requests\PromotionCode;

use App\Models\Promotion;
use App\Models\PromotionCode;
use App\Models\Purchase;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ValidatePromotionCode {

    /**
     * @param \Illuminate\Support\Facades\Validator $validator
     * @param string $promocodeName
     * @param \App\Models\Service $service
     * @param \App\Models\Schedule|null $schedule
     */
    public static function validate(Validator $validator, string $promocodeName, Service $service,
                                    ?Schedule $schedule): void {
        if (!$promoCode = PromotionCode::where('name', $promocodeName)->whereHas('promotion', function($query) {
            $query->where('status', Promotion::STATUS_ACTIVE)->where('expiry_date', '>=', date('Y-m-d H:i:s'));
        })->where('status', PromotionCode::STATUS_ACTIVE)->with(['promotion'])->first()) {
            $validator->errors()->add('promo_code', 'Promo code is invalid!');
        }

        //initialise variables
        $promotion = $promoCode->promotion;
        $eligibleUsers = $promoCode->users()->pluck('users.id');

        $promoDisciplines = $promoCode->promotion->disciplines()->pluck('disciplines.id');
        $promoFocusAreas = $promoCode->promotion->focus_area()->pluck('focus_areas.id');
        $promoServiceTypes = $promoCode->promotion->service_types()->pluck('service_types.id');

        $serviceDisciplines = $service->disciplines()->pluck('disciplines.id');
        $serviceServiceTypes = $service->service_types()->pluck('service_types.id');
        $serviceFocusAreas = $service->focus_areas()->pluck('focus_areas.id');

        if ($eligibleUsers->count() && !$eligibleUsers->has(Auth::id())) {
            $validator->errors()->add('promo_code', 'Promo code user is invalid');
        }

        //Uses Per Code
        if ($promoCode->uses_per_code > 0 && Purchase::where('promocode_id', $promoCode->id)
                                                     ->count() >= (int)$promoCode->uses_per_code) {
                $validator->errors()->add('promo_code', 'Promo code is already in use');
        }

        //Uses Per Client
        if ($promoCode->uses_per_client > 0 && Purchase::where('promocode_id', $promoCode->id)
                                                       ->where('user_id', Auth::id())
                                                       ->count() >= (int)$promoCode->uses_per_client) {
                $validator->errors()->add('promo_code', 'You are not allowed to use this promocode');
        }

        if ($schedule instanceof Schedule) {
            //checks below
            if ($promotion->spend_min && $schedule->cost < $promotion->spend_min) {
                $validator->errors()->add('promo_code', 'Promo eligible for services with price more than'
                                                        .$promotion->spend_min.' only!');
            }

            if ($promotion->spend_max && $schedule->cost > $promotion->spend_max) {
                $validator->errors()->add('promo_code', 'Promo eligible for services with price less than '
                                                        .$promotion->spend_max.'only!');
            }

            if (Carbon::parse($promotion->valid_from) >= Carbon::parse($schedule->start_date)) {
                $validator->errors()
                          ->add('promo_code', 'This promo is only for services starting from '.$promotion->valid_from);
            }

            if (Carbon::parse($promotion->expiry_date) <= Carbon::parse($schedule->end_date)) {
                $validator->errors()->add('promo_code', "Promo code has expired");
            }
        }

        if (count($promoDisciplines) && !count(array_intersect($promoDisciplines, $serviceDisciplines))) {
            $validator->errors()->add('promo_code', 'You are not allowed to use the promocode with this discipline');
        } elseif (count($promoFocusAreas) && !count(array_intersect($promoFocusAreas, $serviceFocusAreas))) {
            $validator->errors()->add('promo_code', 'You are not allowed to use the promocode with this focus area');
        } elseif (count($promoServiceTypes) && !count(array_intersect($promoServiceTypes, $serviceServiceTypes))) {
            $validator->errors()->add('promo_code', 'You are not allowed to use the promocode with this service type');
        }
    }
}
