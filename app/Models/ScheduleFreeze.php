<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleFreeze extends Model {

    use HasFactory;

    protected $fillable = ['freeze_at', 'user_id', 'schedule_id', 'quantity'];

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
