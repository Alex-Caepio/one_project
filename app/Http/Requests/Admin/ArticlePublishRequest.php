<?php

namespace App\Http\Requests\Admin;

use App\Helpers\UserRightsHelper;
use App\Http\Requests\Request;
use Illuminate\Validation\Validator;

class ArticlePublishRequest extends Request {

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
    public function rules() {
        return [];
    }

    public function withValidator(Validator $validator) {
        $validator->setRules([
                                 'title'        => 'required|string|min:5|max:120',
                                 'description'  => 'required|string|min:5|max:3000',
                                 'introduction' => 'required|string|min:5|max:200'
                             ])->setData($this->article->toArray())->validate();

        if (!$validator->fails()) {
            $validator->after(function($validator) {
                if (!$this->article->user->is_admin && !$this->article->user->is_published) {
                    $validator->errors()
                              ->add('is_published', "Please publish your profile before you can publish an article.");
                }

                if (!UserRightsHelper::userAllowToPublishArticle($this->article->user)) {
                    $validator->errors()
                              ->add('is_published', "Please upgrade subscription to be able to publish articles");
                }
            });
        }
    }
}
