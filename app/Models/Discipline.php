<?php

namespace App\Models;

use App\Scopes\PublishedScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Class Discipline
 *
 * @property int id
 * @property bool is_published
 * @property string url
 * @property string name
 * @property string icon_url
 * @property string banner_url
 * @property string description
 * @property string introduction
 * @property Collection media_images
 * @property Collection media_videos
 * @property Collection media_files
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Discipline extends Model
{
    use HasFactory, PublishedScope;

    protected $fillable = [
        'name', 'description', 'introduction', 'url', 'icon_url', 'banner_url',
        'is_published', 'section_2_h2', 'section_2_h3', 'section_2_background',
        'section_2_textarea', 'section_3_h2', 'section_3_h4', 'section_4_h2',
        'section_4_h3', 'section_4_background', 'section_4_textarea',
        'section_5_header_h2', 'section_6_h2', 'section_6_h3', 'section_6_background',
        'section_6_textarea', 'section_7_media_url', 'section_7_tag_line',
        'section_7_alt_text', 'section_7_url', 'section_7_target_blanc',
        'section_8_h2', 'section_9_h2', 'section_9_h3', 'section_9_background',
        'section_9_textarea', 'section_10_h2', 'section_11_media_url',
        'section_11_tag_line', 'section_11_alt_text', 'section_11_url',
        'section_11_target_blanc', 'section_12_h2', 'section_13_media_url',
        'section_13_tag_line', 'section_13_alt_text', 'section_13_url', 'section_13_target_blanc'
    ];

    public function featured_practitioners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'discipline_featured_practitioner', 'discipline_id', 'user_id')
            ->withTimeStamps();
    }

    public function featured_services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'discipline_featured_service', 'discipline_id', 'service_id')
            ->withTimeStamps();
    }

    /**
     * @deprecated
     */
    public function discipline_images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DisciplineImage::class);
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'discipline_article', 'discipline_id', 'article_id')
            ->withTimeStamps();
    }

    public function focus_areas(): BelongsToMany
    {
        return $this->belongsToMany(FocusArea::class, 'discipline_focus_area', 'discipline_id', 'focus_area_id')
            ->withTimeStamps();
    }

    public function practitioners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'discipline_practitioner', 'discipline_id', 'practitioner_id')
            ->withTimeStamps();
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    /**
     * @deprecated
     */
    public function discipline_videos()
    {
        return $this->hasMany(DisciplineVideo::class);
    }

    public function related_disciplines(): BelongsToMany {
        return $this->belongsToMany(__CLASS__, 'discipline_discipline', 'related_id', 'parent_id')->published()->withTimeStamps();
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

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)
            ->orWhere('url', $value)
            ->firstOrFail();
    }

    public function featured_at_focus_area()
    {
        return $this->belongsToMany(FocusArea::class, 'focus_area_featured_discipline', 'discipline_id', 'focus_area_id');
    }

    public function featured_focus_areas()
    {
        return $this->belongsToMany(FocusArea::class, 'discipline_featured_focus_area', 'discipline_id', 'focus_area_id');
    }

    public function featured_articles()
    {
        return $this->belongsToMany(Article::class, 'discipline_featured_articles', 'discipline_id', 'article_id');
    }

    public function featured_main_pages(): BelongsToMany
    {
        return $this->belongsToMany(MainPage::class, 'main_page_featured_discipline', 'discipline_id', 'main_page_id');
    }

    public function featured_disciplines()
    {
        return $this->belongsToMany(FocusArea::class, 'focus_area_featured_discipline', 'focus_area_id', 'discipline_id');
    }

    public function promotions(): BelongsToMany {
        return $this->belongsToMany(Promotion::class, 'promotion_discipline', 'discipline_id', 'promotion_id');
    }
}
