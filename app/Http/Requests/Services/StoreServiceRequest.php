<?php

namespace App\Http\Requests\Services;

use App\Helpers\UserRightsHelper;
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
        return Auth::user()->onlyUnpublishedAllowed();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'           => 'required|string|min:5|max:100',
            'description'     => 'nullable|string|min:5|max:5000',
            'is_published'    => 'bool',
            'introduction'    => 'required|string|min:5|max:500',
            'service_type_id' => 'required|exists:service_types,id',
            'image_url'       => 'nullable|url',
            'icon_url'        => 'nullable|url',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $isPublished = $this->getBoolFromRequest('is_published');
            if ($isPublished && Auth::user()->isFullyRestricted()) {
                $validator->errors()->add('is_published', "Please upgrade subscription or publish profile to be able to publish service");
            }

            // It should be unique in order to open the link.
            if ($this->user()->services()->where('user_id', $this->user()->id)->where('slug', $this->slug)->exists()) {
                $validator->errors()->add('slug', 'Service slug should be unique!');
            }

            $tmpService = new Service(['service_type_id' => $this->service_type_id]);
            if (!UserRightsHelper::userAllowPublishService($this->user(), $tmpService)) {
                $validator->errors()->add('service_type_id', "Please upgrade your subscription to publish this service type");
            }

        });
    }

    protected function getSlug(): string
    {
        $titleSlug = $this->get('title') ?? '';
        return $this->get('slug') ?? to_url($titleSlug);
    }

    protected function prepareForValidation()
    {
        if(!$this->has('slug')) {
            $this->merge([
                'slug' => $this->getSlug(),
            ]);
        }
    }
}
