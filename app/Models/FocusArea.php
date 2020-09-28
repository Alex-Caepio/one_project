<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FocusArea extends Model
{
    use HasFactory;
    protected $fillable = ['name','url','description','introduction'];

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class,'discipline_focus_area','discipline_id','focus_area_id')->withTimeStamps();
    }
    public function focus_area_images()
    {
        return $this->hasMany(FocusAreaImage::class);
    }
    public function practitioners()
    {
        return $this->belongsToMany(User::class,'focus_area_practitioner','focus_area_id','practitioner_id')->withTimeStamps();
    }
    public function focus_area_videos()
    {
        return $this->hasMany(FocusAreaVideo::class);
    }
    public function articles()
    {
        return $this->belongsToMany(Article::class,'focus_area_article','focus_area_id','article_id')->withTimeStamps();
    }
}

