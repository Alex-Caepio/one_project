<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Service extends Model
{
    use SoftDeletes,HasFactory;
    protected $fillable = [
        'name',
        'title',
        'description',
        'introduction',
        'url'
    ];

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
        return $this->belongsToMany(Discipline::class);
    }
    public function focus_areas()
    {
        return $this->belongsToMany(FocusArea::class,'focus_area_service','service_id','focus_area_id')->withTimeStamps();
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
        return $this->belongsToMany(ServiceType::class,'service_type_service','service_id','service_type_id')->withTimeStamps();
    }

    public function favorite()
    {
        return (bool)Favorite::where('user_id', Auth::id())
            ->where('service_id', $this->id)
            ->first();
    }

}
