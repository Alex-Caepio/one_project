<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordClaim extends FormRequest
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
            'email'    => 'required',
            'token'    => 'required|regex:/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}/',
            'password' => 'required|min:6',
        ];
    }
}
