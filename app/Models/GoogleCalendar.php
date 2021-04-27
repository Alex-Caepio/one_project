<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoogleCalendar extends Model
{
    use HasFactory;

    public $table = 'user_google_calendar';

    public $fillable = ['user_id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'access_token',
        'refresh_token',
        'expired_at',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function timezone() {
        return $this->hasOne(Timezone::class, 'id', 'timezone_id');
    }

    public function unavailabilities(): HasMany {
        return $this->hasMany(UserUnavailabilities::class, 'practitioner_id', 'user_id');
    }
}
