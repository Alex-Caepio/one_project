<?php


namespace App\Http\Requests\Admin\RequestTraits;


trait DisciplineValidationFilter
{
    protected function prepareForValidation()
    {
        $this->merge(
            [
                'section_2_textarea' => strip_tags($this->section_2_textarea, '<br>'),
                'section_4_textarea' => strip_tags($this->section_4_textarea, '<br>'),
                'section_6_textarea' => strip_tags($this->section_6_textarea, '<br>'),
                'section_9_textarea' => strip_tags($this->section_9_textarea, '<br>'),
            ]
        );
    }
}
