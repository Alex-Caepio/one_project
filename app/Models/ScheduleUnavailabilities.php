<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float commission_on_sale
 */
class ScheduleUnavailabilities extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'start_date',
        'end_date',
    ];

    public function schedules(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}

