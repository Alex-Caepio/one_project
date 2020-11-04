<?php

namespace App\Models;

use App\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    ];

    public function disciplines() {
        return $this->belongsToMany(Discipline::class, 'discipline_practitioner', 'discipline_id', 'practitioner_id')
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

    public function featured_articles()
    {
        return $this->belongsToMany(FocusArea::class, 'focus_area_featured_article', 'focus_area_id', 'article_id');
    }
}
