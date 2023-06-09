<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleFreeze extends Model
{

    use HasFactory;

    protected $fillable = [
        'freeze_at',
        'user_id',
        'schedule_id',
        'quantity',
        'price_id',
        'practitioner_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'start_at' => 'datetime',
        'freeze_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }


    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function practitioner()
    {
        return $this->belongsTo(User::class, 'practitioner_id', 'id');
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return Carbon::parse($this->freeze_at) >= Carbon::now()->subMinutes(15);
    }
}
