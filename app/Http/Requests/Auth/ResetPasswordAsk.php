<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordAsk extends FormRequest {
    public function withValidator($validator) {
        $result = User::where('email', $this->get('email'))->first();
        $validator->after(function($validator) use ($result) {
            if (!$result) {
                $validator->errors()->add('email', 'The Email is not valid');
            } else if (!$result->email_verified_at) {
                $validator->errors()->add('email', 'Please verify your email before continuing.
A verification email was previously sent to your registered email address');
            }
        });
    }

    /**
     * Authorization rules
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * @return array
     */
    public function rules() {
        return [
            'email' => 'required',
        ];
    }
}
