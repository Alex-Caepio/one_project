<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;


class LoginRequest extends FormRequest
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
            'email'    => 'required|email',
            'password' => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $user = User::where('email', $this->get('email'))->first();
        $validator->after(function ($validator) use ($user) {
            if (!$user || !Hash::check($this->get('password'), $user->password)) {
                $validator->errors()->add('email', 'The Email and Password were not valid');
                return;
            }

            if (!$user->email_verified_at) {
                $validator->errors()->add('email', 'The email has to be verified first');
            }
        });
    }
}
