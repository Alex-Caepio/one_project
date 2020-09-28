<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Article extends Model
{
    use HasFactory;
    protected $fillable=[
        'title','introduction','url'
    ];
    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class,'discipline_practitioner','discipline_id','practitioner_id')->withTimeStamps();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favourite_articles()
    {
        return $this->belongsToMany(Article::class);
    }
    public function articlefavorite()
    {
        return (bool) ArticleFavorite::where('user_id', Auth::id())
            ->where('article_id', $this->id)
            ->first();
    }
}
