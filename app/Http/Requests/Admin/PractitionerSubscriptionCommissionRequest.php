<?php


namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class PractitionerSubscriptionCommissionRequest extends Request {
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
            'user_id'   => [
                'required',
                Rule::exists('users', 'id')->where('account_type', User::ACCOUNT_PRACTITIONER)
                    ->where('is_published', true)
            ],
            'rate'      => 'required',
            'date_from' => 'required_if:is_dateless,false',
            'date_to'   => 'required_if:is_dateless,false',
        ];
    }


    public function withValidator($validator) {
        $validator->after(function ($validator) {
            $user = User::where('id', $this->get('user_id'))->with('plan')->first();
            if (!$user->plan) {
                $validator->errors()->add('user_id', 'Subscription plan must be attached to the practitioner');
            }
        });

    }
}
