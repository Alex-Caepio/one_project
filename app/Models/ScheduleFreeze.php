<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleFreeze extends Model {

    use HasFactory;

    protected $fillable = ['freeze_at', 'user_id', 'schedule_id', 'quantity'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'freeze_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function schedule() {
        return $this->belongsTo(Schedule::class);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }


    /**
     * @return bool
     */
    public function isExpired(): bool {
        return Carbon::parse($this->freeze_at) >= Carbon::now()->subMinutes(15);
    }

}
