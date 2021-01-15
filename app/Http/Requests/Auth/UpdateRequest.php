<?php

namespace App\Http\Requests\Auth;


use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
            'first_name'                  => 'required|max:255|string',
            'last_name'                   => 'required|max:255|string',
            'about_me'                    => 'max:10000',
            'emails_holistify_update'     => 'bool',
            'emails_practitioner_offers'  => 'bool',
            'email_forvard_practitioners' => 'bool',
            'email_forvard_clients'       => 'bool',
            'email_forvard_support'       => 'bool',
            'about_my_busines'            => 'max:10000',
            'busines_name'                => 'required|max:255|gt:2',
            'busines_address'             => 'max:255',
            'busines_email'               => 'required|max:255|email',
            'public_link'                 => 'max:255|url',
            'busines_introduction'        => 'max:255',
            'gender'                      => 'string',
            'date_of_birth'               => 'date',
            'mobile_number'               => 'max:255',
            'busines_phone_number'        => 'max:255',
            'email'                       => 'required|email|unique',
            'email_verified_at'           => 'date_format:Y-m-d H:i:s',
            'password'                    => 'max:45',
            'avatar_url'                  => 'min:5',
            'background_url'              => 'min:5',

            'current_password'            => 'required_with:password',
            'password'                    => 'max:20|min:8|regex:/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]/',
            'first_name'                  => 'string|min:2|max:30',
            'last_name'                   => 'string|min:2|max:30',
            'mobile_country_code'         => 'exists:countries,id|integer|required_with:mobile_number',
            'business_phone_country_code' => 'exists:countries,id|integer|required_with:busines_phone_number',
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
                return;
            }
        });
    }
}
