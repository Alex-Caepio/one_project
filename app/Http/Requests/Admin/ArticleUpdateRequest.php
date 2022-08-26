<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

class ArticleUpdateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title'        => 'required_if:is_published,true|string|min:5|max:120',
            'description'  => 'required_if:is_published,true|string|min:5|max:15000',
            'is_published' => 'required|boolean',
            'introduction' => 'required_if:is_published,true|string|min:5|max:200',
            'image_url'    => 'nullable|url',
        ];
    }


    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $isPublished = $this->getBoolFromRequest('is_published');

            if ($isPublished) {
                if (!$this->article->user->is_admin && !$this->article->user->is_published) {
                    $validator->errors()
                        ->add('is_published', 'Please publish your profile before you can publish an article.');
                } elseif (!$this->article->user->canPublishArticle()) {
                    $validator->errors()
                        ->add('is_published', 'Please upgrade subscription to be able to publish articles.');
                }
            }
        });
    }
}
