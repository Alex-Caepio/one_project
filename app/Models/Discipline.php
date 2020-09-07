<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discipline extends Model
{
    protected $fillable = ['title'];

    public function discipline_images()
    {
        return $this->hasMany(DisciplineImage::class);
    }
    public function articles()
    {
        return $this->belongsToMany(Article::class,'discipline_article','discipline_id','article_id')->withTimeStamps();
    }
    public function focus_areas()
    {
        return $this->belongsToMany(FocusArea::class,'discipline_focus_area','discipline_id','focus_area_id')->withTimeStamps();
    }
    public function practitioners()
    {
        return $this->belongsToMany(User::class,'discipline_practitioner','discipline_id','practitioner_id')->withTimeStamps();
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
