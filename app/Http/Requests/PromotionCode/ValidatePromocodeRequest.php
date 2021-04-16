<?php

namespace App\Http\Requests\PromotionCode;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

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
        $idValue = $this->schedule->prices->pluck('id');
        return [
            'promo_code' => 'required|string|min:1',
            'price_id'   => 'required|exists:prices,id',
            Rule::in($idValue),
            'amount'     => 'required'
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

        $price = $this->schedule->prices()->find($this->get('price_id'));

        $schedule = $this->schedule;
        $service = $this->schedule->service;
        ValidatePromotionCode::validate($validator, $name, $service, $schedule, $price->cost * $this->get('amount'));
    }
}
