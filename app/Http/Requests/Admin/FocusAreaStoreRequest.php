<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\RequestTraits\FocusAreasValidationFilter;
use App\Http\Requests\Request;
use App\Rules\PlainTextSize;

class FocusAreaStoreRequest extends Request
{
    //use FocusAreasValidationFilter;
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
            'name'                      => 'max:200|unique:focus_areas,name',
            'banner_url'                => 'max:200',
            'icon_url'                  => 'max:200',
            'is_published'              => 'nullable|boolean',

            'section_2_h2'              => 'nullable|max:300',
            'section_2_h3'              => 'nullable|max:300',
            'section_2_background'      => 'nullable|min:6|max:7',
            'section_2_textarea'        => ['nullable', new PlainTextSize(5000)],

            'section_3_h2'              => 'nullable|max:300',

            'section_4_tag_line',
            'section_4_alt_text'        => 'nullable|max:150',
            'section_4_url'             => 'nullable|url',
            'section_4_target_blanc'    => 'nullable|boolean',
            'section_4_video_url'       => 'nullable|url',
            'section_4_image_url'       => 'nullable|url',

            'section_5_h2'              => 'nullable|max:300',
            'section_5_h3'              => 'nullable|max:300',
            'section_5_background'      => 'nullable|min:6|max:7',
            'section_5_textarea'        => ['nullable', new PlainTextSize(5000)],

            'section_6_h2'              => 'nullable|max:300',

            'section_7_h2'              => 'nullable|max:300',
            'section_7_h3'              => 'nullable|max:300',
            'section_7_background'      => 'nullable|min:6|max:7',
            'section_7_textarea'        => ['nullable', new PlainTextSize(5000)],

            'section_8_h2'              => 'nullable|max:300',

            'section_9_tag_line',
            'section_9_alt_text'        => 'nullable|max:150',
            'section_9_url'             => 'nullable|url',
            'section_9_target_blanc'    => 'nullable|boolean',
            'section_9_video_url'       => 'nullable|url',
            'section_9_image_url'       => 'nullable|url',

            'section_10_h2'             => 'nullable|max:300',
            'section_10_h3'             => 'nullable|max:300',
            'section_10_background'     => 'nullable|min:6|max:7',
            'section_10_textarea'       => ['nullable', new PlainTextSize(5000)],

            'section_11_h2'             => 'nullable|max:300',
            'section_11_image_url'      => 'nullable|url',
            'section_11_video_url'      => 'nullable|url',

            'section_12_h2'             => 'nullable|max:300',
            'section_12_h4'             => 'nullable|max:300',

            'section_13_tag_line',
            'section_13_alt_text'       => 'nullable|max:150',
            'section_13_url'            => 'nullable|url',
            'section_13_target_blanc'   => 'nullable|boolean',
            'section_13_image_url'      => 'nullable|url',
            'section_13_video_url'      => 'nullable|url',
        ];
    }
}
