<?php

namespace App\Http\Requests\Articles;

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
        return Auth::user()->isPractitioner();
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
                'description'  => 'required|string|min:5|max:3000',
                'is_published' => 'bool',
                'introduction' => 'required|string|min:5|max:200',
                'url'          => 'required|url|unique:articles,url' . ($this->article ? ',' . $this->article->id : ''),
                'image_url'    => 'url'
            ];
        }
        return [
            'title'        => 'string|min:5|max:120',
            'description'  => 'string|min:5|max:3000',
            'is_published' => 'bool',
            'introduction' => 'string|min:5|max:200',
            'url'          => 'url',
            'image_url'    => 'url'
        ];

    }


    public function withValidator(Validator $validator) {
        $validator->after(function($validator) {
            $isPublished = $this->getBoolFromRequest('is_published');
            if (!Auth::user()->is_published && $isPublished) {
                $validator->errors()->add('name',
                                          "Article not Published. Please publish your Business Profile before publishing the Article");
            }
        });
    }


}
