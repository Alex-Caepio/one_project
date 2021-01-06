<?php

namespace App\Http\Requests\PromotionCode;

use App\Models\Promotion;
use App\Models\PromotionCode;
use App\Http\Requests\Request;

class PurchaseRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'promo_code' => 'string|min:5',
        ];
    }

    public function withValidator($validator) {
        $validator->after(function($validator) {
            $this->validatePromo($validator);
        });
    }

    public function validatePromo($validator) {
        if (!$name = $this->get('promo_code')) {
            return true;
        }

        if (!$promoCode = PromotionCode::where('name', $name)->whereHas('promotion', function($query) {
            $query->where('status', Promotion::STATUS_ACTIVE)->where('expiry_date', '>=', date('Y-m-d H:i:s'));
        })->where('status', PromotionCode::STATUS_ACTIVE)->with(['promotion'])->first()) {
            $validator->errors()->add('promo_code', 'Promo code is invalid!');
        }

        //initialise variables
        $promotion = $promoCode->promotion;
        $schedule = $this->schedule;
        $eligableUsers = $promoCode->users()->pluck('users.id');

        $promoDisciplines = $promoCode->promotion->disciplines()->pluck('disciplines.id');
        $promoFocusAreas = $promoCode->promotion->focus_area()->pluck('focus_areas.id');
        $promoServiceTypes = $promoCode->promotion->service_types()->pluck('service_types.id');

        $service = $this->schedule->service;

        $serviceDisciplines = $service->disciplines()->pluck('disciplines.id');
        $serviceServiceTypes = $service->service_types()->pluck('service_types.id');
        $serviceFocusAreas = $service->focus_areas()->pluck('focus_areas.id');

        //checks below
        if ($promotion->spend_min && $schedule->cost < $promotion->spend_min) {
            $validator->errors()->add('promo_code',
                                      "Promo eligable for services with price more than {$promotion->spend_min} only!");
        }

        if ($promotion->spend_max && $schedule->cost > $promotion->spend_max) {
            $validator->errors()->add('promo_code',
                                      "Promo eligable for services with price less than {$promotion->spend_max} only!");
        }

        if ($eligableUsers->count() && !$eligableUsers->has($this->user()->id)) {
            $validator->errors()->add('promo_code', 'Promo code is invalid');
        }

        if ($promotion->valid_from >= $schedule->start_date) {
            $validator->errors()
                      ->add('promo_code', "This promo is only for services starting from {$promotion->valid_from}");
        }

        if ($promotion->expiry_date <= $schedule->end_date) {
            $validator->errors()->add('promo_code', "Promo code has expired");
        }

        if (count($promoDisciplines) && !count(array_intersect($promoDisciplines, $serviceDisciplines))) {
            $validator->errors()->add('promo_code', "You are not allowed to use the promocode with this discipline");
        } elseif (count($promoFocusAreas) && !count(array_intersect($promoFocusAreas, $serviceFocusAreas))) {
            $validator->errors()->add('promo_code', "You are not allowed to use the promocode with this focus area");
        } elseif (count($promoServiceTypes) && !count(array_intersect($promoServiceTypes, $serviceServiceTypes))) {
            $validator->errors()->add('promo_code', "You are not allowed to use the promocode with this service type");
        }
    }
}
