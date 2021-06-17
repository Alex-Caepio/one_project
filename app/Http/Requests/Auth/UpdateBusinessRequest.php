<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdateBusinessRequest extends Request {
    /**
     * Authorization rules
     *
     * @return bool
     */
    public function authorize() {
        return Auth::user()->isPractitioner();
    }

    /**
     * @return array
     */
    public function rules() {
        return [
            'is_published'                => 'bool',
            'business_name'               => 'required|max:255|min:2',
            'business_address'            => 'required|max:255',
            'business_email'              => 'required|max:255|email',
            'business_vat'                => 'nullable|string|max:20',
            'business_postal_code'        => 'required|string|max:20',
            'business_city'               => 'required|string|max:150',
            'business_phone_number'       => 'required|digits_between:2,255|numeric',
            'business_phone_country_code' => 'required|exists:countries,id|integer',
            'business_country_id'         => 'required|exists:countries,id|integer'
        ];
    }


    public function withValidator($validator) {
        $user = $this->user();

        $validator->after(function($validator) use ($user) {
            if ($this->getBoolFromRequest('is_published') === true || $user->is_published) {
                if (!$user->business_name && !$this->business_name) {
                    $validator->errors()->add('business_name', 'You have not filled in the field "Business name"');
                }
                if (!$user->business_address && !$this->business_address) {
                    $validator->errors()
                              ->add('business_address', 'You have not filled in the field "Business address"');
                }
                if (!$user->business_email && !$this->business_email) {
                    $validator->errors()->add('business_email', 'You have not filled in the field "Business email"');
                }

                if (!$user->business_city && !$this->business_city) {
                    $validator->errors()->add('business_city', 'You have not filled in the field "Business City"');
                }

            }
        });

        $validator->after(function($validator) use ($user) {
            if ($this->get('current_password') && !Hash::check($this->get('current_password'), $user->password)) {
                $validator->errors()->add('current_password', 'The current password is incorrect!');
            }
        });
    }
}
