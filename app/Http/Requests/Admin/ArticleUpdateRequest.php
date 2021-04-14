<?php

namespace App\Http\Requests\Admin;

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
        $isPublished = $this->getBoolFromRequest('is_published');
        if ($isPublished === true) {
            return [
                'title'        => 'required|string|min:5|max:120',
                'description'  => 'required|string|min:5|max:3000',
                'is_published' => 'required|boolean',
                'introduction' => 'required|string|min:5|max:200',
                'image_url'    => 'nullable|url',
                'user_id'      => 'required|exists:users,id|integer|min:0'
            ];
        }
        return [
            'title'        => 'string|min:5|max:120',
            'description'  => 'string|min:5|max:3000',
            'is_published' => 'required|boolean',
            'introduction' => 'string|min:5|max:200',
            'image_url'    => 'nullable|url',
            'user_id'      => 'required|exists:users,id|integer|min:0'
        ];

    }


    public function withValidator(Validator $validator) {

        $validator->after(function($validator) {
            $isPublished = $this->getBoolFromRequest('is_published');
            if (!$this->article->user->is_published && $isPublished) {
                $validator
                    ->errors()
                    ->add('name',
                          'Article not Published. The practitioner must be published before publishing the Article');
            }
        });
    }


}
