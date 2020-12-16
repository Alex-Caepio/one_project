<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class MainPageUpdateRequest extends Request
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
            'section_1_image_url'               => 'required|url',
            'section_1_alt_text'                => 'required|max:150|min:3',
            'section_1_intro_text'              => 'required|max:150|min:3',

            'section_2_background'              => 'nullable|min:6|max:7',

            'section_3_h1'                      => 'nullable|max:150',
            'section_3_h2'                      => 'nullable|max:300',
            'section_3_background'              => 'nullable|min:6|max:7',
            'section_3_button_text'             => 'nullable|max:20',
            'section_3_button_color'            => 'nullable|min:6|max:7',
            'section_3_button_url'              => 'nullable|url',
            'section_3_text'                    => 'nullable|max:2000',
            'section_3_target_blanc'            => 'nullable|boolean',

            'section_4_h2'                      => 'nullable|max:150',

            'section_5_h2'                      => 'nullable|max:150',
            'section_5_h3'                      => 'nullable|max:300',
            'section_5_background'              => 'nullable|min:6|max:7',
            'section_5_text'                    => 'nullable|max:2000',

            'section_6_h1'                      => 'nullable|max:150',
            'section_6_h3'                      => 'nullable|max:300',
            'section_6_button_text'             => 'nullable|max:20',
            'section_6_button_color'            => 'nullable|min:6|max:7',
            'section_6_button_url'              => 'nullable|url',
            'section_6_target_blanc'            => 'nullable|boolean',
            'section_6_text'                    => 'nullable|max:2000',
            'section_6_media_url'               => 'nullable|url',
            'section_6_alt_text'                => 'nullable|max:150',

            'section_7_h2'                      => 'nullable|max:150',

            'section_8_h1'                      => 'nullable|max:150',
            'section_8_h3'                      => 'nullable|max:300',
            'section_8_background'              => 'nullable|min:6|max:7',
            'section_8_text'                    => 'nullable|max:2000',

            'section_9_h2'                      => 'nullable|max:150',

            'section_10_h2'                     => 'nullable|max:150',
            'section_10_h3'                     => 'nullable|max:300',
            'section_10_text'                   => 'nullable|max:2000',
            'section_10_media_url'              => 'nullable|url',
            'section_10_alt_text'               => 'nullable|max:150',

            'section_11_h2'                     => 'nullable|max:150',
            'section_11_h3'                     => 'nullable|max:300',
            'section_11_text'                   => 'nullable|max:2000',
            'section_11_button_text'            => 'nullable|max:20',
            'section_11_button_url'             => 'nullable|url',
            'section_11_button_color'           => 'nullable|min:6|max:7',
            'section_11_target_blanc'           => 'nullable|boolean',
            'section_11_media_url'              => 'nullable|url',
            'section_11_alt_text'               => 'nullable|max:150',

            'section_12_h2'                     => 'nullable|max:150',
            'section_12_text'                   => 'nullable|max:2000',
            'section_12_media_1_media_url'      => 'nullable|url',
            'section_12_media_1_url'            => 'nullable|url',
            'section_12_media_1_target_blanc'   => 'nullable|boolean',
            'section_12_media_2_media_url'      => 'nullable|url',
            'section_12_media_2_url'            => 'nullable|url',
            'section_12_media_2_target_blanc'   => 'nullable|boolean',
            'section_12_media_3_media_url'      => 'nullable|url',
            'section_12_media_3_url'            => 'nullable|url',
            'section_12_media_3_target_blanc'   => 'nullable|boolean',
            'section_12_media_4_media_url'      => 'nullable|url',
            'section_12_media_4_url'            => 'nullable|url',
            'section_12_media_4_target_blanc'   => 'nullable|boolean',
            'section_12_media_5_media_url'      => 'nullable|url',
            'section_12_media_5_url'            => 'nullable|url',
            'section_12_media_5_target_blanc'   => 'nullable|boolean',
            'section_12_media_6_media_url'      => 'nullable|url',
            'section_12_media_6_url'            => 'nullable|url',
            'section_12_media_6_target_blanc'   => 'nullable|boolean',
        ];

    }

}
