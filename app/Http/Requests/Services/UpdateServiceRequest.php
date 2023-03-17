<?php

namespace App\Http\Requests\Services;

use App\Helpers\UserRightsHelper;
use App\Http\Requests\Request;
use App\Rules\Slug;
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
        return Auth::user()->is_admin || ($this->service->user->onlyUnpublishedAllowed() && $this->service->user_id === Auth::user()->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'        => 'string|min:5|max:100',
            'description'  => 'nullable|string',
            'is_published' => 'bool',
            'introduction' => 'string|min:5|max:500',
            'image_url'    => 'nullable|url',
            'icon_url'     => 'nullable|url',
            'slug'         => [
                'nullable',
                new Slug(),
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            // check for length
            if (!empty($this->description)) {
                $descriptionLen = strlen(strip_tags($this->description));
                if ($descriptionLen < 5) {
                    $validator->errors()->add('description', "Description minimum length is 5");
                }

                if ($descriptionLen > 5000) {
                    $validator->errors()->add('description', "Description maximum length is 5000");
                }
            }

            $isPublished = $this->getBoolFromRequest('is_published');

            if ($isPublished && $this->service->user->isFullyRestricted()) {
                $validator->errors()->add('is_published', "Please upgrade subscription or publish profile to be able to publish service");
            }

            // It should be unique in order to open the link.
            $slugNotUnique = $this->service->user->services()
                    ->where('user_id', $this->service->user->id)
                    ->where('id', '!=', $this->service->id)
                    ->where('slug', $this->slug)
                    ->exists()
                ;

            if ($slugNotUnique) {
                $validator->errors()->add('slug', 'Service slug should be unique!');
            }

            // Check schedules
            if (($this->get('service_type_id') !== $this->service->service_type_id) &&
                $this->service->schedules()->count()) {
                $validator->errors()->add('service_type_id', 'You are not able change service type for service with existing schedules');
                return;
            }

            if (!UserRightsHelper::userAllowPublishService($this->service->user, $this->service)) {
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
        if (!$this->has('slug')) {
            $this->merge([
                'slug' => $this->getSlug(),
            ]);
        }
    }
}
