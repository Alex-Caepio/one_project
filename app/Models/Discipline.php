<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
            ->withTimeStamps();
    }

}
