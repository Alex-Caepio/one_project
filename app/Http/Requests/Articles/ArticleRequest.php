<?php

namespace App\Http\Requests\Articles;

use App\Helpers\UserRightsHelper;
use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ArticleRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return !Auth::user()->isFullyRestricted();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        $isPublished = $this->getBoolFromRequest('is_published');
        if ($isPublished === true) {
            return [
                'title'        => 'required|string|min:5|max:120',
                'description'  => 'required|string|min:5|max:15000',
                'is_published' => 'required|boolean',
                'introduction' => 'required|string|min:5|max:200',
                'image_url'    => 'nullable|url',
            ];
        }
        return [
            'title'        => 'string|min:5|max:120',
            'description'  => 'string|min:5|max:15000',
            'is_published' => 'required|boolean',
            'introduction' => 'string|min:5|max:200',
            'image_url'    => 'nullable|url',
        ];

    }


    public function withValidator(Validator $validator) {
        $validator->after(function($validator) {
            $isPublished = $this->getBoolFromRequest('is_published');
            if ($isPublished && !UserRightsHelper::userAllowToPublishArticle(Auth::user())) {
                    $validator->errors()
                              ->add('is_published', "Please upgrade subscription to be able to publish articles");
                }
        });
    }


}
