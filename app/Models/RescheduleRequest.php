<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RescheduleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'user_id',
        'new_schedule_id',
        'booking_id',
        'new_datetime_from',
        'new_price_id',
        'comment'
    ];

    public function old_schedule(){
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function new_schedule(){
        return $this->belongsTo(Schedule::class, 'new_schedule_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function booking(){
        return $this->belongsTo(Booking::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
