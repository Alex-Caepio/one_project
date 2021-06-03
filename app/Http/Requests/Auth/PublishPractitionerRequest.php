<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PublishPractitionerRequest extends Request {
    /**
     * Authorization rules
     *
     * @return bool
     */
    public function authorize() {
        return Auth::user()->isPractitioner();
    }

    /**
     * @return array
     */
    public function rules() {
        return [
            'about_my_business'     => 'max:15000',
            'avatar_url'            => 'nullable|url',
            'background_url'        => 'nullable|url',
            'business_city'         => 'required|string|max:150',
            'business_name'         => 'required|max:255|min:2',
            'business_introduction' => 'sometimes|string|max:150',
            'business_country_id'   => 'required|exists:countries,id|integer',
            'slug'                  => [
                Rule::unique('users', 'slug')->ignore($this->user()->id),
            ],
            'is_published'          => 'required|bool',
        ];
    }

}
