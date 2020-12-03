<?php

namespace App\Http\Requests\Services;

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
        $service = $this->route('service');
        return $service->user_id === Auth::id();
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
                                 'description'     => 'string|min:5|max:1000',
                                 'is_published'    => 'boolean',
                                 'introduction'    => 'required|min:5|max:500',
                                 'url'             => 'required|url|unique:services,url,' . $this->service->id,
                                 'image_url'       => 'url',
                                 'icon_url'        => 'url',
                                 'service_type_id' => 'required|exists:service_types,id'
                             ])->setData($this->service->toArray())->validate();
        if (!$validator->fails()) {
            $validator->after(function($validator) {
                if (!$this->service->user->is_published) {
                    $validator->errors()->add('name', "Please publish Business Profile before publishing the Article");
                }
            });
        }
    }
}
