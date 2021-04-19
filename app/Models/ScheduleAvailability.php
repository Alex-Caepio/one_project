<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

/**
 * @property string days
 * @property string start_time
 * @property string end_time
 */
class ScheduleAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'days',
        'start_time',
        'end_time',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function fits(string $datetime): bool
    {
        return $this->fitsDay($datetime) && $this->fitsTime($datetime);
    }

    public function fitsDay(string $datetime): bool {

        $daysFormatted = strtolower($this->days);

        if ($daysFormatted === 'everyday') {
            return true;
        }

        $datetime = Carbon::createFromFormat('d-m-Y', $datetime);

        if ($daysFormatted === 'weekdays' && $datetime->isWeekday()) {
            return true;
        }
        if ($daysFormatted === 'weekends' && $datetime->isWeekend()) {
            return true;
        }

        return strtolower($datetime->dayName) === $daysFormatted;
    }

    public function fitsTime(string $datetime): bool {
        if ($this->start_time === $this->end_time) {
            return true;
        }

        $time = (new Carbon($datetime))->format('H:i:s');
        $timeStart = Carbon::createFromTimeString($this->start_time);
        $timeEnd = Carbon::createFromTimeString($this->end_time);

        if ($timeStart->isAfter($timeEnd)) {
            $timeEnd->addDay();
        }

        return Carbon::createFromTimeString($time)->between($timeStart, $timeEnd);
    }
}

