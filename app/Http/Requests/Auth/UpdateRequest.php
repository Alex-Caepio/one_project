<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdateRequest extends FormRequest
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
            'about_me'                    => 'max:10000',
            'emails_holistify_update'     => 'bool',
            'emails_practitioner_offers'  => 'bool',
            'email_forward_practitioners' => 'bool',
            'email_forward_clients'       => 'bool',
            'email_forward_support'       => 'bool',
            'about_my_business'            => 'max:10000',
            'business_name'               => 'sometimes|required|max:255|min:2',
            'business_address'            => 'max:255',
            'business_email'              => 'sometimes|required|max:255|email',
            'public_link'                 => 'max:255|url',
            'business_introduction'       => 'max:255',
            'gender'                      => 'string',
            'date_of_birth'               => 'date',
            'mobile_number'               => 'max:255',
            'business_phone_number'       => 'max:255',
            'email'                       => 'sometimes|required|email|unique:users,email',
            'email_verified_at'           => 'date_format:Y-m-d H:i:s',
            'avatar_url'                  => 'min:5',
            'background_url'              => 'min:5',

            'current_password'            => 'required_with:password',
            'password'                    => 'max:20|min:8|regex:/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]/',
            'first_name'                  => 'string|min:2|max:30',
            'last_name'                   => 'string|min:2|max:30',
            'mobile_country_code'         => 'exists:countries,id|integer|required_with:mobile_number',
            'business_phone_country_code' => 'exists:countries,id|integer|required_with:business_phone_number',
        ];
    }

    public function messages()
    {
        return [
            'password.regex' => 'The password must include both uppercase and lowercase letters and at least one number'
        ];

    }

    public function withValidator($validator)
    {
        $user = $this->user();
        $validator->after(function ($validator) use ($user) {
            if ($this->get('current_password') && !Hash::check($this->get('current_password'), $user->password)) {
                $validator->errors()->add('current_password', 'The current password is incorrect!');
            }
        });
    }
}
