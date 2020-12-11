<?php

namespace App\Transformers;

use App\Models\MainPage;

class MainPageTransformer extends Transformer
{
    protected $availableIncludes = [
        'featured_focus_areas', 'featured_disciplines',
        'featured_practitioners', 'featured_services'
    ];

    public function transform(MainPage $mainPage): array
    {
        return [
            'id'                   => $mainPage->id,
            'section_1_image_url'  => $mainPage->section_1_image_url,
            'section_1_video_url'  => $mainPage->section_1_video_url,
            'section_1_alt_text'   => $mainPage->section_1_alt_text,
            'section_1_intro_text' => $mainPage->section_1_intro_text,

            'section_2_background' => $mainPage->section_2_background,

            'section_3_h1'           => $mainPage->section_3_h1,
            'section_3_h2'           => $mainPage->section_3_h2,
            'section_3_background'   => $mainPage->section_3_background,
            'section_3_button_text'  => $mainPage->section_3_button_text,
            'section_3_button_color' => $mainPage->section_3_button_color,
            'section_3_button_url'   => $mainPage->section_3_button_url,
            'section_3_text'         => $mainPage->section_3_text,
            'section_3_target_blanc' => (bool)$mainPage->section_3_target_blanc,

            'section_4_h2' => $mainPage->section_4_h2,

            'section_5_h2'         => $mainPage->section_5_h2,
            'section_5_h3'         => $mainPage->section_5_h3,
            'section_5_background' => $mainPage->section_5_background,
            'section_5_text'       => $mainPage->section_5_text,

            'section_6_h1'           => $mainPage->section_6_h1,
            'section_6_h3'           => $mainPage->section_6_h3,
            'section_6_button_text'  => $mainPage->section_6_button_text,
            'section_6_button_color' => $mainPage->section_6_button_color,
            'section_6_button_url'   => $mainPage->section_6_button_url,
            'section_6_target_blanc' => (bool)$mainPage->section_6_target_blanc,
            'section_6_text'         => $mainPage->section_6_text,
            'section_6_image_url'    => $mainPage->section_6_image_url,
            'section_6_video_url'    => $mainPage->section_6_video_url,
            'section_6_alt_text'     => $mainPage->section_6_alt_text,

            'section_7_h2' => $mainPage->section_7_h2,

            'section_8_h1'         => $mainPage->section_8_h1,
            'section_8_h3'         => $mainPage->section_8_h3,
            'section_8_background' => $mainPage->section_8_background,
            'section_8_text'       => $mainPage->section_8_text,

            'section_9_h2' => $mainPage->section_9_h2,

            'section_10_h2'        => $mainPage->section_10_h2,
            'section_10_h3'        => $mainPage->section_10_h3,
            'section_10_text'      => $mainPage->section_10_text,
            'section_10_image_url' => $mainPage->section_10_image_url,
            'section_10_video_url' => $mainPage->section_10_video_url,
            'section_10_alt_text'  => $mainPage->section_10_alt_text,

            'section_11_h2'           => $mainPage->section_11_h2,
            'section_11_h3'           => $mainPage->section_11_h3,
            'section_11_text'         => $mainPage->section_11_text,
            'section_11_button_text'  => $mainPage->section_11_button_text,
            'section_11_button_url'   => $mainPage->section_11_button_url,
            'section_11_button_color' => $mainPage->section_11_button_color,
            'section_11_target_blanc' => (bool)$mainPage->section_11_target_blanc,
            'section_11_image_url'    => $mainPage->section_11_image_url,
            'section_11_video_url'    => $mainPage->section_11_video_url,
            'section_11_alt_text'     => $mainPage->section_11_alt_text,

            'section_12_h2'                   => $mainPage->section_12_h2,
            'section_12_h3'                   => $mainPage->section_12_h3,
            'section_12_media_1_image_url'    => $mainPage->section_12_media_1_image_url,
            'section_12_media_1_url'          => $mainPage->section_12_media_1_url,
            'section_12_media_1_target_blanc' => (bool)$mainPage->section_12_media_1_target_blanc,
            'section_12_media_2_image_url'    => $mainPage->section_12_media_2_image_url,
            'section_12_media_2_url'          => $mainPage->section_12_media_2_url,
            'section_12_media_2_target_blanc' => (bool)$mainPage->section_12_media_2_target_blanc,
            'section_12_media_3_image_url'    => $mainPage->section_12_media_3_image_url,
            'section_12_media_3_url'          => $mainPage->section_12_media_3_url,
            'section_12_media_3_target_blanc' => (bool)$mainPage->section_12_media_3_target_blanc,
            'section_12_media_4_image_url'    => $mainPage->section_12_media_4_image_url,
            'section_12_media_4_url'          => $mainPage->section_12_media_4_url,
            'section_12_media_4_target_blanc' => (bool)$mainPage->section_12_media_4_target_blanc,
            'section_12_media_5_image_url'    => $mainPage->section_12_media_5_image_url,
            'section_12_media_5_url'          => $mainPage->section_12_media_5_url,
            'section_12_media_5_target_blanc' => (bool)$mainPage->section_12_media_5_target_blanc,
            'section_12_media_6_image_url'    => $mainPage->section_12_media_6_image_url,
            'section_12_media_6_url'          => $mainPage->section_12_media_6_url,
            'section_12_media_6_target_blanc' => (bool)$mainPage->section_12_media_6_target_blanc,

            'created_at' => $this->dateTime($mainPage->created_at),
            'updated_at' => $this->dateTime($mainPage->updated_at),
        ];
    }

    public function includeFeaturedFocusAreas(MainPage $mainPage)
    {
        return $this->collectionOrNull($mainPage->featured_focus_areas, new FocusAreaTransformer());
    }

    public function includeFeaturedDisciplines(MainPage $mainPage)
    {
        return $this->collectionOrNull($mainPage->featured_disciplines, new DisciplineTransformer());
    }

    public function includeFeaturedPractitioners(MainPage $mainPage)
    {
        return $this->collectionOrNull($mainPage->featured_practitioners, new UserTransformer());
    }

    public function includeFeaturedServices(MainPage $mainPage)
    {
        return $this->collectionOrNull($mainPage->featured_services, new ServiceTransformer());
    }
}
