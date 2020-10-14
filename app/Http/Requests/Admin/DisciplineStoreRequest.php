<?php

namespace App\Http\Requests\Admin;

use App\Models\Discipline;
use App\Http\Requests\Request;

class DisciplineStoreRequest extends Request
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
            'name' => 'required|unique:disciplines,name',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $url       = $this->get('url') ?? to_url($this->get('name'));
            $fieldName = $this->get('url') ? 'url' : 'name';

            if (Discipline::where('url', $url)->exists()) {
                $validator->errors()->add($fieldName, "The slug {$url} is not unique! Please, chose the different {$fieldName}.");
            }
        });
    }
}
