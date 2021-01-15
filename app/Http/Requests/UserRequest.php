<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        return [
            'is_published'                => 'bool',
            'subscription_id'             => 'integer',
            'account_type'                => 'required|string',
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
            'busines_address'             => 'required|max:255',
            'busines_email'               => 'required|max:255|email',
            'public_link'                 => 'required|max:255|url',
            'busines_introduction'        => 'required|max:255',
            'gender'                      => 'required|string',
            'date_of_birth'               => 'required|date',
            'mobile_number'               => 'max:255',
            'busines_phone_number'        => 'max:255',
            'email'                       => 'required|email|unique',
            'email_verified_at'           => 'date_format:Y-m-d H:i:s',
            'password'                    => 'required|max:45',
            'avatar_url'                  => 'min:5',
            'background_url'              => 'min:5',
        ];
    }

    public function messages()
    {
        return [
            'email.unique'                  => 'Email is not available',
            'email_verified_at.date_format' => 'Invalid date format',
        ];

    }
}
