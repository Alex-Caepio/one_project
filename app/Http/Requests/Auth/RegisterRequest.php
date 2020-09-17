<?php

namespace App\Http\Requests\Auth;


use Illuminate\Foundation\Http\FormRequest;

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
            'email'      => 'required|email|max:255|unique:users',
            'password'   => 'required|regex:/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}/',
            'first_name' => 'required|string|min:2|max:30',
            'last_name'  => 'required|string|min:2|max:30',
        ];
    }
}
