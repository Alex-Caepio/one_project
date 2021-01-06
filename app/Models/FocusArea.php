<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FocusArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'url', 'description', 'introduction', 'banner_url', 'icon_url', 'is_published',
        'section_2_h2', 'section_2_h3', 'section_2_background',
        'section_2_textarea', 'section_3_h2', 'section_4_tag_line', 'section_4_alt_text',
        'section_4_url', 'section_4_target_blanc', 'section_5_h2', 'section_5_h3',
        'section_5_background', 'section_5_textarea', 'section_6_header_h2', 'section_7_h2',
        'section_7_h3', 'section_7_background', 'section_7_text', 'section_8_h2', 'section_9_tag_line',
        'section_9_alt_text', 'section_9_url', 'section_9_target_blanc', 'section_10_h2',
        'section_10_h3', 'section_10_background', 'section_10_textarea', 'section_11_h2',
        'section_12_h2', 'section_12_h4', 'section_13_tag_line', 'section_13_alt_text',
        'section_13_url', 'section_13_target_blanc'
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class, 'discipline_focus_area', 'focus_area_id', 'discipline_id')->where('is_published', true)->withTimeStamps();
    }

    public function focus_area_images()
    {
        return $this->hasMany(FocusAreaImage::class);
    }

    public function practitioners()
    {
        return $this->belongsToMany(User::class, 'focus_area_practitioner', 'focus_area_id', 'practitioner_id')->withTimeStamps();
    }

    public function focus_area_videos()
    {
        return $this->hasMany(FocusAreaVideo::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'focus_area_article', 'focus_area_id', 'article_id')->withTimeStamps();
    }

    public function featured_practitioners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'focus_area_featured_user', 'focus_area_id', 'user_id');
    }

    public function featured_disciplines(): BelongsToMany
    {
        return $this->belongsToMany(Discipline::class, 'focus_area_featured_discipline', 'focus_area_id', 'discipline_id');
    }

    public function featured_articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'focus_area_featured_article', 'focus_area_id', 'article_id');
    }

    public function featured_services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'focus_area_featured_service', 'focus_area_id', 'service_id');
    }

    public function featured_focus_areas(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'focus_area_featured_focus_area', 'parent_focus_area_id', 'child_focus_area_id');
    }

    public function featured_at_disciplines(): BelongsToMany
    {
        return $this->belongsToMany(Discipline::class, 'discipline_featured_focus_area', 'focus_area_id', 'discipline_id');
    }

    public function featured_main_pages(): BelongsToMany
    {
        return $this->belongsToMany(MainPage::class, 'main_page_featured_focus_area', 'focus_area_id', 'main_page_id');
    }

    public function media_images()
    {
        return $this->morphMany(MediaImage::class, 'morphesTo', 'model_name', 'model_id');
    }

    public function media_videos()
    {
        return $this->morphMany(MediaVideo::class, 'morphesTo', 'model_name', 'model_id');
    }

    public function media_files()
    {
        return $this->morphMany(MediaFile::class, 'morphesTo', 'model_name', 'model_id');
    }

    public function promotions(): BelongsToMany {
        return $this->belongsToMany(Promotion::class, 'promotion_focus_area', 'focus_area_id', 'promotion_id');
    }

}

