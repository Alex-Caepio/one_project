<?php

namespace App\Http\Requests\Auth;

use App\Traits\PublishPractitionerRequestValidatorTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdateMediaRequest extends Request {

    use PublishPractitionerRequestValidatorTrait;
    /**
     * Authorization rules
     *
     * @return bool
     */
    public function authorize() {
        return Auth::user()->isPractitioner() || Auth::user()->id_admin;
    }

    /**
     * @return array
     */
    public function rules() {
        return [
            'about_my_business'     => 'max:15000',
            'avatar_url'            => 'nullable|url',
            'background_url'        => 'nullable|url',
            'business_city'         => 'required_if:is_published,true|string|max:150',
            'business_name'         => 'required_if:is_published,true|max:255|min:2',
            'business_introduction' => 'sometimes|string|max:150',
            'business_country_id'   => 'required|exists:countries,id|integer',
            'slug'                  => [
                Rule::unique('users', 'slug')->ignore($this->user()->id),
            ],
            'is_published'          => 'required|bool',
        ];
    }


    public function withValidator($validator) {
       $this->validatePublishState($validator);
    }

}
