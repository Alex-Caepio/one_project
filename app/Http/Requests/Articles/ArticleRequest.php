<?php

namespace App\Http\Requests\Articles;

use App\Helpers\UserRightsHelper;
use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class ArticleRequest extends Request {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return Auth::user()->onlyUnpublishedAllowed();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        return [
            'title'        => 'required_if:is_published,true|string|min:5|max:120|regex:#^[a-zA-Z0-9_-]*$#',
            'description'  => 'required_if:is_published,true|string|min:5|max:15000',
            'is_published' => 'required|boolean',
            'introduction' => 'required_if:is_published,true|string|min:5|max:200',
            'image_url'    => 'nullable|url',
            'slug'         => 'nullable|regex:#^[a-zA-Z0-9_-]*$#',
        ];
    }


    public function withValidator(Validator $validator) {
        $validator->after(function($validator) {
            $isPublished = $this->getBoolFromRequest('is_published');
            if ($isPublished) {
                if (!Auth::user()->is_admin && !Auth::user()->is_published) {
                    $validator->errors()
                              ->add('is_published', "Please publish your profile before you can publish an article.");
                } elseif (!UserRightsHelper::userAllowToPublishArticle(Auth::user())) {
                    $validator->errors()
                              ->add('is_published', "Please upgrade subscription to be able to publish articles");
                }
            }
        });
    }


}
