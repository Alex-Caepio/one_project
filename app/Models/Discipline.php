<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discipline extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'url'];

    public function featured_practitioners()
    {
        return $this->belongsToMany(User::class, 'featured_practitioners', 'discipline_id', 'user_id')->withTimeStamps();
    }

    public function featured_services()
    {
        return $this->belongsToMany(Service::class, 'featured_services', 'discipline_id', 'service_id')->withTimeStamps();
    }

    public function discipline_images()
    {
        return $this->hasMany(DisciplineImage::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'discipline_article', 'discipline_id', 'article_id')->withTimeStamps();
    }

    public function focus_areas()
    {
        return $this->belongsToMany(FocusArea::class, 'discipline_focus_area', 'discipline_id', 'focus_area_id')->withTimeStamps();
    }

    public function practitioners()
    {
        return $this->belongsToMany(User::class, 'discipline_practitioner', 'discipline_id', 'practitioner_id')->withTimeStamps();
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function discipline_videos()
    {
        return $this->hasMany(DisciplineVideo::class);
    }

}
