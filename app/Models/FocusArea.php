<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FocusArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'url', 'description', 'introduction', 'banner_url', 'icon_url',
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
        return $this->belongsToMany(Discipline::class, 'discipline_focus_area', 'discipline_id', 'focus_area_id')->where('is_published', true)->withTimeStamps();
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

    public function featured_practitioners(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'focus_area_featured_user', 'user_id', 'focus_area_id');
    }

    public function featured_disciplines(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Discipline::class, 'focus_area_featured_discipline', 'discipline_id', 'focus_area_id');
    }

    public function featured_articles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'focus_area_featured_article', 'article_id', 'focus_area_id');
    }

    public function featured_services(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'focus_area_featured_service', 'service_id', 'focus_area_id');
    }

    public function featured_focus_areas(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'focus_area_featured_focus_area', 'child_focus_area_id', 'parent_focus_area_id');
    }
}

