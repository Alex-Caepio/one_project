<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdateRequest extends Request
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
        $user = $this->user();
        return [
            'about_me'                    => 'max:10000',
            'emails_holistify_update'     => 'bool',
            'emails_practitioner_offers'  => 'bool',
            'email_forward_practitioners' => 'bool',
            'email_forward_clients'       => 'bool',
            'is_published'                => 'bool',
            'email_forward_support'       => 'bool',
            'about_my_business'           => 'max:2000',
            'business_name'               => 'sometimes|required|max:255|min:2',
            'business_address'            => 'sometimes|required|max:255',
            'business_email'              => 'sometimes|required|max:255|email',
            'slug'                        => ['nullable',
            Rule::unique('users','slug')->ignore($this->user()->id)],
            'business_introduction'       => 'max:150',
            'gender'                      => 'string',
            'date_of_birth'               => 'sometimes|nullable|date|before:-18 years',
            'mobile_number'               => 'digits_between:2,255|numeric',
            'business_phone_number'       => 'digits_between:2,255|numeric',
            'email'                       => ['sometimes',
                'required',
                'email',
                Rule::unique('users','email')->ignore($this->user()->id)
            ],
            'email_verified_at'           => 'date_format:Y-m-d H:i:s',

            'current_password'            => 'required_with:password',
            'password'                    => 'max:20|min:8|regex:/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]/',
            'first_name'                  => 'string|min:2|max:30',
            'last_name'                   => 'string|min:2|max:30',
            'mobile_country_code'         => 'exists:countries,id|integer|required_with:mobile_number',
            'business_phone_country_code' => 'exists:countries,id|integer|required_with:business_phone_number',
            'cancel_bookings_on_unpublish' => ['bool',
                Rule::requiredIf(
                    $user->isPractitioner() &&
                    $user->is_published == true &&
                    $user->bookings()->exists() &&
                    $this->is_published == false
                )]
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
        $validator->after(function ($validator) use($user){
            if ($this->getBoolFromRequest('is_published') === true || $user->is_published){
                if (!$user->business_name && !$this->business_name) {
                    $validator->errors()->add(
                        'business_name',
                        'You have not filled in the field "Business name"'
                    );
                }
                if (!$user->business_address && !$this->business_address) {
                    $validator->errors()->add(
                        'business_address',
                        'You have not filled in the field "Business address"'
                    );
                }
                if (!$user->business_email && !$this->business_email) {
                    $validator->errors()->add(
                        'business_email',
                        'You have not filled in the field "Business email"'
                    );
                }
                if (!$user->business_introduction && !$this->business_introduction) {
                    $validator->errors()->add(
                        'business_introduction',
                        'You have not filled in the field "Business introduction"'
                    );
                }
                if (!$user->business_country && !$this->business_country) {
                    $validator->errors()->add(
                        'business_country',
                        'You have not filled in the field "Business Country"'
                    );
                }
                if (!$user->business_city && !$this->business_city) {
                    $validator->errors()->add(
                        'business_city',
                        'You have not filled in the field "Business City"'
                    );
                }
                if (!$user->business_time_zone_id && !$this->business_time_zone_id) {
                    $validator->errors()->add(
                        'business_time_zone_id',
                        'You have not filled in the field "Timezone"'
                    );
                }
            }
        });
        $validator->after(function ($validator) use ($user) {
            if ($this->get('current_password') && !Hash::check($this->get('current_password'), $user->password)) {
                $validator->errors()->add('current_password', 'The current password is incorrect!');
            }
        });
    }
}
