<?php

namespace App\Http\Requests\PaymentMethod;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentMethodRequest extends FormRequest
{
    /**
     * Authorization rules
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'billing_details.address.postal_code' => 'required',
            'billing_details.name' => 'required',
            'card.exp_month' => 'required|min:2',
            'card.exp_year' => 'required|min:2',
        ];
    }

    public function messages()
    {
        return [];
    }
}
