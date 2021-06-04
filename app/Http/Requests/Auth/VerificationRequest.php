<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
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
