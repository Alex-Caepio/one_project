<?php

namespace App\Http\Requests\Auth;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'email'      => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')
            ],
            'password'                => 'required|max:20|min:8|regex:/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]/',
            'first_name'              => 'required|string|min:2|max:30',
            'last_name'               => 'required|string|min:2|max:30',
            'account_type'            => [
                'required',
                Rule::in(['client', 'practitioner']),
            ],
            'accepted_terms'          => 'required|accepted',
            'emails_holistify_update' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'email.unique'   => 'Email is not available',
            'password.regex' => 'The password must include both uppercase and lowercase letters and at least one number'
        ];
    }
}
