<?php

namespace App\Models;

use App\Traits\Fits;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property Carbon $start_date
 * @property Carbon $end_date
 */
class UserUnavailabilities extends Model
{
    use HasFactory, Fits;

    protected $fillable = [
        'practitioner_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public $timestamps = true;

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'practitioner_id', 'id');
    }

    public function calendar(): HasOne
    {
        return $this->hasOne(GoogleCalendar::class, 'practitioner_id', 'id');
    }
}
