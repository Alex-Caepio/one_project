<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class ResetPasswordAsk extends FormRequest
{
    public function withValidator($validator)
    {
        $result = User::where('email', $this->get('email'))->first();
        $validator->after(function ($validator) use ($result) {
            if (!$result) {
                $validator->errors()->add('email', 'The Email is not valid');
            }
            if (!$result->email_verified_at) {
                $validator->errors()->add('email', 'Please verify your email before continuing.
An email has been sent to the email address you registered to verify');
            }
        });
    }
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
            'email' => 'required',
        ];
    }
}
