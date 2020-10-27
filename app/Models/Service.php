<?php

namespace App\Models;

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
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'title',
        'description',
        'introduction',
        'url'
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
        return $this->belongsToMany(Discipline::class)->where('is_published', true);
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
        return $this->belongsToMany(Service::class);
    }

    public function service_types()
    {
        return $this->belongsToMany(ServiceType::class, 'service_type_service', 'service_id', 'service_type_id')->withTimeStamps();
    }

    public function favorite()
    {
        return (bool)Favorite::where('user_id', Auth::id())
            ->where('service_id', $this->id)
            ->first();
    }

}
