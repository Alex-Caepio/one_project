<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Fits;

/**
 * @property int id
 * @property int schedule_id
 * @property Carbon start_date
 * @property Carbon end_date
 */
class ScheduleUnavailability extends Model
{
    use HasFactory, Fits;

    protected $fillable = [
        'schedule_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}
