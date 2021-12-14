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
        'new_price_id',
        'comment',
        'old_location_displayed',
        'new_location_displayed',
        'old_url',
        'new_url',
        'old_start_date',
        'new_start_date',
        'old_end_date',
        'new_end_date',
        'old_price_id',
        'requested_by',
        'noreply_sent',
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

    public function old_price() {
        return $this->belongsTo(Price::class, 'old_price_id');
    }

    public function isAmendment(): bool {
        return $this->schedule_id === $this->new_schedule_id;
    }
}
