<?php

namespace App\Http\Requests\Articles;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'=>'min:5',
            'description'=>'min:5',
            'user_id'=>'exists:App\Models\User,id',
            'is_published'=>'boolean',
            'introduction'=>'min:5',
            'url'=> 'url'
        ];
    }
}
