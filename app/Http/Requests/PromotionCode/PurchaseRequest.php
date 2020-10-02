<?php

namespace App\Http\Requests\PromotionCode;

use App\Http\Requests\Request;
use App\Models\PromotionCode;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'promo_code' => 'string|min:5',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validatePromo($validator);
        });
    }

    public function validatePromo($validator)
    {
        if (!$name = $this->get('promo_code')) {
            return true;
        }

        if (!$promo = PromotionCode::where('name', $name)->first()) {
            $validator->errors()->add('promo_code', 'Promo code is invalid!');

        }

        //initialise variables
        $promotion = $promo->promotion;
        $schedule = $this->schedule;
        $eligableUsers = $promo->users()->pluck('users.id');
        $service = $this->schedule->service;
        $promoDiscipline = $promo->promotion->discipline_id;
        $serviceDiscipline = $service->disciplines()->first()->id;
        $promoServiceType = $promo->promotion->service_type_id;
        $serviceServiceType = $service->service_types()->first()->id;
        $promoFocusArea = $promo->promotion->focus_area_id;
        $serviceFocusArea = $service->focus_areas()->first()->id;

        //checks below
        if ($promotion->spend_min && $schedule->cost < $promotion->spend_min) {
            $validator->errors()->add('promo_code', "Promo eligable for services with price more than {$promotion->spend_min} only!");
        }

        if ($promotion->spend_max && $schedule->cost > $promotion->spend_max) {
            $validator->errors()->add('promo_code', "Promo eligable for services with price less than {$promotion->spend_max} only!");
        }

        if ($eligableUsers->count() && !$eligableUsers->has($this->user()->id)) {
            $validator->errors()->add('promo_code', 'Promo code is invalid');
        }

        if ($promotion->valid_from >= $schedule->start_date) {
            $validator->errors()->add('promo_code', "This promo is only for services starting from {$promotion->valid_from}");
        }

        if ($promotion->expiry_date <= $schedule->end_date) {
            $validator->errors()->add('promo_code', "Promo code has expired");
        }
        if (!$promoDiscipline == $serviceDiscipline && !$promoServiceType == $serviceServiceType && !$promoFocusArea == $serviceFocusArea) {
            $validator->errors()->add('promo_code', "Promo code is invalid");
        }
    }
}
