<?php


namespace App\Http\Requests\Admin\RequestTraits;


trait FocusAreasValidationFilter
{

    protected function prepareForValidation()
    {
        $this->merge([
                         'section_2_textarea' => strip_tags($this->section_2_textarea, '<br>'),
                         'section_5_textarea' => strip_tags($this->section_5_textarea, '<br>'),
                         'section_7_textarea' => strip_tags($this->section_7_textarea, '<br>'),
                         'section_10_textarea' => strip_tags($this->section_10_textarea, '<br>'),
                     ]);
    }

}
