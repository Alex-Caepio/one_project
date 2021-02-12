<?php

namespace App\Models;

use App\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Article extends Model {

    use HasFactory, SoftDeletes, PublishedScope;

    protected $fillable = [
        'title',
        'introduction',
        'url',
        'description',
        'image_url'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function disciplines() {
        return $this->belongsToMany(Discipline::class, 'discipline_article', 'article_id', 'discipline_id')
                    ->published()
                    ->withTimeStamps();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function favourite_articles() {
        return $this->belongsToMany(__CLASS__);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function media_images() {
        return $this->morphMany(MediaImage::class, 'morphesTo', 'model_name', 'model_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function media_videos() {
        return $this->morphMany(MediaVideo::class, 'morphesTo', 'model_name', 'model_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function media_files() {
        return $this->morphMany(MediaFile::class, 'morphesTo', 'model_name', 'model_id');
    }

    public function featured_focus_area()
    {
        return $this->belongsToMany(FocusArea::class, 'focus_area_featured_focus_area', 'discipline_id', 'focus_area_id');
    }

    public function featured_at_disciplines(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Discipline::class, 'discipline_feature_articles', 'article_id', 'discipline_id');
    }
    public function featured_articles() {
        return $this->belongsToMany(FocusArea::class, 'focus_area_featured_article', 'focus_area_id', 'article_id');
    }

    public function focus_areas(): BelongsToMany {
        return $this->belongsToMany(FocusArea::class, 'focus_area_article', 'article_id', 'focus_area_id')->withTimeStamps();
    }

    public function keywords(): BelongsToMany {
        return $this->belongsToMany(Keyword::class)->withTimeStamps();
    }

    public function services(): BelongsToMany {
        return $this->belongsToMany(Service::class)->withTimeStamps();
    }
}
