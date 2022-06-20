<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DisciplinePublishRequest extends FormRequest
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
            //
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $discipline = $this->discipline;
            if (!$discipline->name) {
                $validator->errors()->add('name', "You have not filled in the field \"Discipline name\"");
            }
            if (!$discipline->slug) {
                $validator->errors()->add('slug', "You have not filled in the field \"Discipline slug\"");
            }

            if (!$discipline->icon_url) {
                $validator->errors()->add('icon_url', "You have not filled in the field \"Icon url\"");
            }
        });
    }
}
