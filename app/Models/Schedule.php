<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Schedule extends Model
{
    protected $fillable = [
        'title',
        'location_id'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function soldOut()
    {
        $time = Carbon::now()->subMinutes(15);
        $purchased = ScheduleUser::where('schedule_id', $this->id)->count();
        $personalFreezed = ScheduleFreeze::where('schedule_id', $this->id)
            ->where('user_id', Auth::id())
            ->where('freeze_at', '>', $time->toDateTimeString())->count();
        $freezed = ScheduleFreeze::where('schedule_id', $this->id)
            ->where('freeze_at', '>', $time->toDateTimeString())->count();
        if (isset($personalFreezed)) {
            return (bool)$this->attendees <= $purchased + $freezed - $personalFreezed;
        } else
            return (bool)$this->attendees <= $purchased + $freezed;

    }
}
