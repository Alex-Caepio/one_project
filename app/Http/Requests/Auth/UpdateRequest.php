<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UpdateRequest extends Request {
    /**
     * Authorization rules
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * @return array
     */
    public function rules() {
        return [
            'date_of_birth'       => 'nullable|date|before:-18 years',
            'mobile_number'       => 'digits_between:2,255|numeric',
            'email'               => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()->id)
            ],
            'current_password'    => 'required_with:password',
            'password'            => 'max:20|min:8|regex:/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]/',
            'first_name'          => 'string|min:2|max:30',
            'last_name'           => 'string|min:2|max:30',
            'mobile_country_code' => 'exists:countries,id|integer|required_with:mobile_number',
            'country_id'          => 'nullable|integer|exists:countries,id',
        ];
    }

    public function messages() {
        return [
            'password.regex' => 'The password must include both uppercase and lowercase letters and at least one number'
        ];

    }

    public function withValidator($validator) {
        $validator->after(function($validator) {
            if ($this->get('current_password') &&
                !Hash::check($this->get('current_password'), $this->user()->password)) {
                $validator->errors()->add('current_password', 'The current password is incorrect!');
            }
        });
    }
}
