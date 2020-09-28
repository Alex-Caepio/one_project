<?php

namespace App\Http\Requests\Auth;

use Carbon\Carbon;
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

    public function withæValidator($validator): void
    {
        $validator->after(function ($validator) {
            $validToken   = DB::table('password_resets')->where('token', $this->get('token'))->first();
            $createdToken = Carbon::parse($validToken->created_at)->addHours(48);
            if ($createdToken < Carbon::now()) {
                $validator->errors()->add('token', 'The token has been expired');
            }
        });
    }
}
