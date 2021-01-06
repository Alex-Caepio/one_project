<?php

namespace App\Http\Requests\Admin;

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
                                 'url'          => 'required|url|unique:articles,url,' . $this->article->id,
                                 'description'  => 'required|string|min:5|max:3000',
                                 'introduction' => 'required|string|min:5|max:200'
                             ])->setData($this->article->toArray())->validate();
        if (!$validator->fails()) {
            $validator->after(function($validator) {
                if (!$this->article->user->is_published) {
                    $validator->errors()->add('name', "Please publish Business Profile before publishing the Article");
                }
            });
        }
    }
}
