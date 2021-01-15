<?php

namespace App\Http\Requests\Plans;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class PlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    public function withValidator($validator, StripeClient $stripe)
    {
        $payment_method = $stripe->paymentMethods->retrieve(
            $this->payment_method_id,
            []
        );

        $stripe_id = Auth::user()->stripe_customer_id;


        if($payment_method->customer != $stripe_id) {
            $validator->errors()->add('payment_method_id', 'The card does not belong to the user.');
        }
    }

}
