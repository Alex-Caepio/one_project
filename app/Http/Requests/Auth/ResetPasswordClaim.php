<?php

namespace App\Http\Requests\Auth;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
            'token'    => 'required',
            'password' => 'required|max:20|min:8|regex:/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]/',
        ];
    }
    public function messages()
    {
        return [
            'password.regex'   => 'The password must include both uppercase and lowercase letters and at least one number'
        ];

    }
    public function withValidator($validator): void
    {
        $validToken   = DB::table('password_resets')->where('token', $this->get('token'))->first();
        $createdToken = Carbon::parse($validToken->created_at)->addHours(48);
        $validator->after(function ($validator) use ($createdToken) {
            if ($createdToken < Carbon::now()) {
                $validator->errors()->add('token', 'The token has been expired');
            }
        });
    }
}
