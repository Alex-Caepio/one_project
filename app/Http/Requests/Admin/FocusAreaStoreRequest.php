<?php

namespace App\Http\Requests\Admin;

use App\Models\FocusArea;
use App\Http\Requests\Request;

class FocusAreaStoreRequest extends Request
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
            'name'          => 'max:200',
            'url'           => 'url',
            'banner_url'    => 'max:200',
            'icon_url'      => 'max:200'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if($validator->errors()->isNotEmpty()){
                return;
            }
            $url       = $this->get('url') ?? to_url($this->get('name'));
            $fieldName = $this->get('url') ? 'url' : 'name';

            if (FocusArea::where('url', $url)->exists()) {
                $validator->errors()->add($fieldName, "The slug {$url} is not unique! Please, chose the different {$fieldName}.");
            }
        });
    }
}
