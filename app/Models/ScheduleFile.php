<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property float commission_on_sale
 */
class ScheduleFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'url',
        'name',
    ];

    public function  schedules()
    {
        return $this->belongsTo(Schedule::class);
    }
}

