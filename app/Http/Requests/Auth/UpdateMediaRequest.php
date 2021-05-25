<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdateMediaRequest extends Request {
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

    public function withValidator($validator) {
        $user = $this->user();
        $validator->after(function($validator) use ($user) {
            if ($this->getBoolFromRequest('is_published') === true || $user->is_published) {

                if (!$user->business_name && !$this->business_name) {
                    $validator->errors()->add('business_name', 'You have not filled in the field "Business name"');
                }

                if (!$user->business_address && !$this->business_address) {
                    $validator->errors()
                              ->add('business_address', 'You have not filled in the field "Business address"');
                }

                if (!$user->business_email && !$this->business_email) {
                    $validator->errors()->add('business_email', 'You have not filled in the field "Business email"');
                }

                if (!$user->business_introduction && !$this->business_introduction) {
                    $validator->errors()->add('business_introduction',
                                              'You have not filled in the field "Business introduction"');
                }

                if (!$user->business_city && !$this->business_city) {
                    $validator->errors()->add('business_city', 'You have not filled in the field "Business City"');
                }
            }
        });
    }
}
