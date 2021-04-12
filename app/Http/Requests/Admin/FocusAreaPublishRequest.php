<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FocusAreaPublishRequest extends FormRequest
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
            $focusArea = $this->focusArea;
            if (!$focusArea->name) {
                $validator->errors()->add('name', "You have not filled in the field \"Focus area name\"");
            }
            if (!$focusArea->introduction) {
                $validator->errors()->add('introduction', "You have not filled in the field \"Introduction\"");
            }
            if (!$focusArea->icon_url) {
                $validator->errors()->add('icon_url', "You have not filled in the field \"Icon url\"");
            }
        });
    }
}
