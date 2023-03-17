<?php

namespace App\Http\Requests\Plans;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class FinalizeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'payment_method_id' => [
                'required',
                'string'
            ],
            'token' => [
                'required',
                'string'
            ],
            'intent_id' => [
                'required',
                'string'
            ]
        ];
    }

}
