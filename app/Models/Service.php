<?php

namespace App\Models;

use App\Scopes\PublishedScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Class Service
 *
 * @property int    id
 * @property int    user_id
 * @property int    is_published
 * @property string url
 * @property string title
 * @property string string
 * @property string description
 * @property string introduction
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Service extends Model
{
    use SoftDeletes, HasFactory, PublishedScope;

    protected $fillable = [
        'title',
        'description',
        'introduction',
        'url',
        'service_type_id'
    ];

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class);
    }

    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class)->published();
    }

    public function focus_areas()
    {
        return $this->belongsToMany(FocusArea::class, 'focus_area_service', 'service_id', 'focus_area_id')->withTimeStamps();
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function favourite_services()
    {
        return $this->belongsToMany(__CLASS__);
    }

    public function service_type()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function featured_focus_area()
    {
        return $this->belongsToMany(FocusArea::class, 'focus_area_featured_service', 'service_id', 'focus_area_id');
    }

    public function featured_main_pages(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(MainPage::class, 'main_page_featured_service', 'service_id', 'main_page_id');
    }

    public function featured_services()
    {
        return $this->belongsToMany(FocusArea::class, 'focus_area_featured_service', 'focus_area_id', 'service_id');
    }

    public function articles() {
        return $this->belongsToMany(Article::class)->published();
    }
}
