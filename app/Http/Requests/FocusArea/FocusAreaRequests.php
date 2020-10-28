<?php

namespace App\Http\Requests\FocusArea;

use Illuminate\Foundation\Http\FormRequest;

class FocusAreaRequests extends FormRequest
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
}
