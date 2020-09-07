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
            'password'   => 'required|string|min:6',
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
        ];
    }
}
