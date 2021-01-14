<?php

namespace App\Http\Requests\PromotionCode;

use App\Models\Promotion;
use App\Models\PromotionCode;
use App\Http\Requests\Request;

class ValidatePromocodeRequest extends Request {
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
            'promo_code' => 'required|string|min:5',
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

        $schedule = $this->schedule;
        $service = $this->schedule->service;
        ValidatePromotionCode::validate($validator, $name, $service, $schedule);
    }
}
