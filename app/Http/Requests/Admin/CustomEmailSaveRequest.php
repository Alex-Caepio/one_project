<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use App\Models\CustomEmail;
use Illuminate\Validation\Rule;

class CustomEmailSaveRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
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
            'from_email'    => 'email|max:255',
            'from_title'    => 'string|max:255',
            'subject'       => 'required|string|max:255',
            'text'          => 'required|string',
            'logo'          => 'nullable|string',
            'logo_filename' => 'nullable|string',
            'delay'         => 'nullable|integer',
            'user_type'     => [
                'required',
                Rule::in([CustomEmail::ALL_EMAIL, CustomEmail::CLIENT_EMAIL, CustomEmail::PRACTITIONER_EMAIL]),
            ],
        ];
    }
}
