<?php

namespace App\Http\Requests\Services;

use App\Helpers\UserRightsHelper;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class ServicePublishRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return !Auth::user()->isFullyRestricted() && ($this->service->user_id === Auth::id() || Auth::user()->is_admin);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [];
    }

    public function withValidator(Validator $validator) {
        $validator->setRules([
                                 'title'           => 'required|min:5|max:100',
                                 'description'     => 'nullable|string|min:5|max:1000',
                                 'is_published'    => 'boolean',
                                 'introduction'    => 'required|min:5|max:500',
                                 'image_url'       => 'nullable|url',
                                 'icon_url'        => 'nullable|url',
                                 'service_type_id' => 'required|exists:service_types,id'
                             ])->setData($this->service->toArray())->validate();
        if (!$validator->fails()) {
            $validator->after(function($validator) {
                if ($this->service->user->isFullyRestricted()) {
                    $validator->errors()->add('name', "Please publish Business Profile before publishing the Article");
                }

                if (!UserRightsHelper::userAllowPublishService($this->service->user, $this->service)) {
                    $validator->errors()->add('name', "You are not able to publish this type of service");
                }
            });
        }
    }
}
