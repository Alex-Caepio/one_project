<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_1_image_url',
        'section_1_video_url',
        'section_1_alt_text',
        'section_1_intro_text',

        'section_2_background',

        'section_3_h1',
        'section_3_h2',
        'section_3_background',
        'section_3_button_text',
        'section_3_button_color',
        'section_3_button_url',
        'section_3_text',
        'section_3_target_blanc',

        'section_4_h2',

        'section_5_h2',
        'section_5_h3',
        'section_5_background',
        'section_5_text',

        'section_6_h1',
        'section_6_h3',
        'section_6_button_text',
        'section_6_button_color',
        'section_6_button_url',
        'section_6_target_blanc',
        'section_6_text',
        'section_6_image_url',
        'section_6_video_url',
        'section_6_alt_text',

        'section_7_h2',

        'section_8_h1',
        'section_8_h3',
        'section_8_background',
        'section_8_text',

        'section_9_h2',

        'section_10_h2',
        'section_10_h3',
        'section_10_text',
        'section_10_image_url',
        'section_10_video_url',
        'section_10_alt_text',

        'section_11_h2',
        'section_11_h3',
        'section_11_text',
        'section_11_button_text',
        'section_11_button_url',
        'section_11_button_color',
        'section_11_target_blanc',
        'section_11_image_url',
        'section_11_video_url',
        'section_11_alt_text',

        'section_12_h2',
        'section_12_text',
        'section_12_media_1_image_url',
        'section_12_media_1_url',
        'section_12_media_1_target_blanc',
        'section_12_media_2_image_url',
        'section_12_media_2_url',
        'section_12_media_2_target_blanc',
        'section_12_media_3_image_url',
        'section_12_media_3_url',
        'section_12_media_3_target_blanc',
        'section_12_media_4_image_url',
        'section_12_media_4_url',
        'section_12_media_4_target_blanc',
        'section_12_media_5_image_url',
        'section_12_media_5_url',
        'section_12_media_5_target_blanc',
        'section_12_media_6_image_url',
        'section_12_media_6_url',
        'section_12_media_6_target_blanc',
    ];

    public function featured_focus_areas(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(FocusArea::class, 'main_page_featured_focus_area', 'main_page_id', 'focus_area_id');
    }

    public function featured_disciplines(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Discipline::class, 'main_page_featured_discipline', 'main_page_id', 'discipline_id');
    }

    public function featured_practitioners(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'main_page_featured_practitioner', 'main_page_id', 'user_id');
    }

    public function featured_services(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'main_page_featured_service', 'main_page_id', 'service_id');
    }
}
