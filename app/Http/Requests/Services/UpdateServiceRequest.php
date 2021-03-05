<?php

namespace App\Http\Requests\Services;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class UpdateServiceRequest extends Request
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
                'title'           => 'string|min:5|max:100',
                'description'     => 'nullable|string|min:5|max:1000',
                'is_published'    => 'bool',
                'introduction'    => 'string|min:5|max:500',
                'url'             => 'url',
                'image_url'       => 'nullable|url',
                'icon_url'        => 'nullable|url',
            ];
        }
        return [
            'title'           => 'string|min:5|max:100',
            'description'     => 'nullable|string|min:5|max:1000',
            'is_published'    => 'boolean',
            'introduction'    => 'string|min:5|max:500',
            'url'             => 'url',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $titleNotUnique = $this->user()->services()
                ->where('user_id', $this->user()->id)
                ->where('id', '!=', $this->service->id)
                ->where('title', $this->get('title'))
                ->exists();

            $urlNotUnique = $this->user()->services()
                ->where('user_id', $this->user()->id)
                ->where('id', '!=', $this->service->id)
                ->where('url', $this->get('url'))
                ->exists();

            if ($titleNotUnique) {
                $validator->errors()->add('title', 'Service name should be unique!');
            }

            if ($urlNotUnique) {
                $validator->errors()->add('url', 'Service url should be unique!');
            }
        });
    }
}
