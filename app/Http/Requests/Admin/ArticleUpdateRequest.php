<?php

namespace App\Http\Requests\Admin;

use App\Helpers\UserRightsHelper;
use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

class ArticleUpdateRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        return [
            'title'        => 'required_if:is_published,true|string|min:5|max:120',
            'description'  => 'required_if:is_published,true|string|min:5|max:15000',
            'is_published' => 'required|boolean',
            'introduction' => 'required_if:is_published,true|string|min:5|max:200',
            'image_url'    => 'nullable|url'
        ];
    }


    public function withValidator(Validator $validator) {
        $validator->after(function($validator) {
            $isPublished = $this->getBoolFromRequest('is_published');
            if ($isPublished && !UserRightsHelper::userAllowToPublishArticle($this->article->user)) {
                $validator->errors()->add('is_published',
                                          "Please upgrade subscription plan or publish practitioner to be able to publish articles");
            }
        });
    }


}
