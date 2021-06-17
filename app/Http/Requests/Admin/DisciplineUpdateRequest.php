<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use App\Models\Discipline;
use Illuminate\Validation\Rule;

class DisciplineUpdateRequest extends Request
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
            'name'                      =>  Rule::unique('disciplines','name')->ignore($this->discipline->name, 'name'),
            'introduction'              => 'max:200',
            'description'               => 'max:200',
            'banner_url'                => 'max:200',
            'icon_url'                  => 'max:200',
            'is_published'              => 'nullable|boolean',

            'section_2_h2'              => 'nullable|max:300',
            'section_2_h3'              => 'nullable|max:300',
            'section_2_background'      => 'nullable|min:6|max:7',
            'section_2_textarea'        => 'nullable|max:5000',

            'section_3_h2'              => 'nullable|max:300',
            'section_3_h4'              => 'nullable|max:300',

            'section_4_h2'              => 'nullable|max:300',
            'section_4_h3'              => 'nullable|max:300',
            'section_4_background'      => 'nullable|min:6|max:7',
            'section_4_textarea'        => 'nullable|max:5000',

            'section_5_header_h2'       => 'nullable|max:300',

            'section_6_h2'              => 'nullable|max:300',
            'section_6_h3'              => 'nullable|max:300',
            'section_6_background'      => 'nullable|min:6|max:7',
            'section_6_textarea'        => 'nullable|max:5000',

            'section_7_tag_line',
            'section_7_alt_text'        => 'nullable|max:150',
            'section_7_url'             => 'nullable|url',
            'section_7_target_blanc'    => 'nullable|boolean',
            'section_7_image_url'       => 'nullable|url',
            'section_7_video_url'       => 'nullable|url',

            'section_8_h2'              => 'nullable|max:300',

            'section_9_h2'              => 'nullable|max:300',
            'section_9_h3'              => 'nullable|max:300',
            'section_9_background'      => 'nullable|min:6|max:7',
            'section_9_textarea'        => 'nullable|max:5000',

            'section_10_h2'             => 'nullable|max:300',

            'section_11_tag_line',
            'section_11_alt_text'       => 'nullable|max:150',
            'section_11_url'            => 'nullable|url',
            'section_11_target_blanc'   => 'nullable|boolean',
            'section_11_image_url'       => 'nullable|url',
            'section_11_video_url'       => 'nullable|url',

            'section_12_h2'             => 'nullable|max:300',

            'section_13_tag_line',
            'section_13_alt_text'       => 'nullable|max:150',
            'section_13_url'            => 'nullable|url',
            'section_13_target_blanc'   => 'nullable|boolean',
            'section_13_image_url'       => 'nullable|url',
            'section_13_video_url'       => 'nullable|url',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $slug       = $this->get('slug') ?? to_url($this->get('name'));
            $fieldName = $this->get('slug') ? 'slug' : 'name';

            if (Discipline::where('slug', $slug)->where('id', '<>', $this->discipline->id)->exists()) {
                $validator->errors()->add(
                    $fieldName,
                    "The slug {$slug} is not unique! Please, choose the different {$fieldName}."
                );
            }
        });
    }
}
