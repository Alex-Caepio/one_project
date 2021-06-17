<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class VerificationRequest extends FormRequest {
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
            'email' => ['required', 'email', Rule::exists('users', 'email')->whereNull('email_verified_at')]
        ];
    }

}
