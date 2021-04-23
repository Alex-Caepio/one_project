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
        return [
            'title'           => 'string|min:5|max:100',
            'description'     => 'nullable|string|min:5|max:1000',
            'is_published'    => 'bool',
            'introduction'    => 'string|min:5|max:500',
            'image_url'       => 'nullable|url',
            'icon_url'        => 'nullable|url',
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

            $slugNotUnique = $this->user()->services()
                ->where('user_id', $this->user()->id)
                ->where('id', '!=', $this->service->id)
                ->where('slug', $this->slug)
                ->exists();

            if ($titleNotUnique) {
                $validator->errors()->add('title', 'Service name should be unique!');
            }

            if ($slugNotUnique) {
                $validator->errors()->add('slug', 'Service slug should be unique!');
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
