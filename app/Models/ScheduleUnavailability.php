<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int    id
 * @property int    schedule_id
 * @property string end_date
 * @property string start_date
 */
class ScheduleUnavailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'start_date',
        'end_date',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function fits(string $datetime)
    {
        $time      = new Carbon($datetime);
        $timeStart = new Carbon($this->start_date);
        $timeEnd   = new Carbon($this->end_date);

        return $time->between($timeStart, $timeEnd);
    }
}

