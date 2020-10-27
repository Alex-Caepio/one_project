<?php

namespace App\Models;

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
    use HasFactory;

    protected $fillable = [
        'name', 'url', 'icon_url', 'banner_url',
        'introduction', 'description'
    ];

    public function featured_practitioners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'featured_practitioners', 'discipline_id', 'user_id')
            ->withTimeStamps();
    }

    public function featured_services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'featured_services', 'discipline_id', 'service_id')
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

    public function related_disciplines(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'discipline_discipline', 'related_id', 'parent_id')
            ->where('is_published', true)->withTimeStamps();
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
}
