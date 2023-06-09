<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallmentCalendar extends FormRequest
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
            'period'   => 'required|integer|min:1',
            'price_id' => 'required|exists:prices,id',
            'amount'   => 'required|integer|min:1',
            'promo_code' => 'nullable|string'
        ];
    }
}
