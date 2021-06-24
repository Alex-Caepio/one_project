<?php

namespace App\Http\Requests\Plans;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class PlanTrialRequest extends FormRequest {
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
        return [];
    }

    public function withValidator($validator) {
        $plan = $this->plan;
        if (!$plan->isActiveTrial()) {
            $validator->errors()->add('plan', 'Please, select plan with free period');
        }
    }
}
