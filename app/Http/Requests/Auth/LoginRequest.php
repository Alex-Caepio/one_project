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
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $result = User::where('email', $this->get('email'))->first();
        $validator->after(function ($validator) use ($result) {
            if (!$result || !Hash::check($this->get('password'), $result->password)) {
                $validator->errors()->add('email', 'The Email and Password were not valid');
            }
        });
    }
}
