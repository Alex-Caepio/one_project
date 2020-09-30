<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class AdminUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|min:2|max:30',
            'last_name'  => 'required|string|min:2|max:30',
            'email'      => 'required|email|max:255|unique:users',
            'current_password'=>'max:20|min:8|regex:/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]/',
            'password'   => 'required|max:20|min:8|regex:/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]/',
        ];

    }
    public function messages()
    {
        return [
            'email.unique'     => 'Email is not available',
            'current_password.regex' => 'The password must include both uppercase and lowercase letters and at least one number',
            'password.regex'   => 'The password must include both uppercase and lowercase letters and at least one number'
        ];

    }
    public function withValidator($validator)
    {

        $validator->after(function ($validator) {
            if (!Hash::check($this->get('current_password'),$this->user()->password)) {
                $validator->errors()->add('current_password', 'The Current password were not valid');
            }
        });
    }

}
