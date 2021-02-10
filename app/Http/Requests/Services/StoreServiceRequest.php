<?php

namespace App\Http\Requests\Services;

use App\Http\Requests\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class StoreServiceRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->isPractitioner();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $isPublished = $this->getBoolFromRequest('is_published');
        $url         = $this->get('url') ?? to_url($this->get('title'));
        $this->getInputSource()->set('url', $url);

        if ($isPublished === true) {
            return [
                'title'           => 'required|string|min:5|max:100',
                'description'     => 'nullable|string|min:5|max:1000',
                'is_published'    => 'bool',
                'introduction'    => 'required|string|min:5|max:500',
                'url'             => 'required',
                'service_type_id' => 'required|exists:service_types,id',
                'image_url'       => 'nullable|url',
                'icon_url'        => 'nullable|url',
            ];
        }
        return [
            'title'           => 'required|string|min:5|max:100',
            'description'     => 'nullable|string|min:5|max:1000',
            'is_published'    => 'boolean',
            'introduction'    => 'required|string|min:5|max:500',
            'url'             => 'required',
            'service_type_id' => 'required|exists:service_types,id'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->user()->services()->where('user_id', '!=', $this->user()->id)->where('title', $this->get('title'))->exists()) {
                $validator->errors()->add('title', 'Service name should be unique!');
            }
            if ($this->user()->services()->where('user_id', '!=', $this->user()->id)->where('url', $this->get('url'))->exists()) {
                $validator->errors()->add('url', 'Service url should be unique!');
            }
        });
    }
}
