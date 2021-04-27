<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserUnavailabilities extends Model {
    use HasFactory;

    protected $fillable = [
        'practitioner_id',
        'start_date',
        'end_date',
    ];

    public function user(): HasOne {
        return $this->hasOne(User::class, 'practitioner_id', 'id');
    }

    public function calendar(): HasOne {
        return $this->hasOne(GoogleCalendar::class, 'practitioner_id', 'id');
    }

}
