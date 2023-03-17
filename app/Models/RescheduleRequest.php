<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $requested_by
 * @property int $new_schedule_id
 * @property int $new_price_id
 * @property string $new_start_date
 * @property string $new_end_date
 *
 * @method static static|Builder where()
 * @method static self|null first()
 */
class RescheduleRequest extends Model
{
    use HasFactory;

    public const REQUESTED_BY_PRACTITIONER = 'practitioner';
    public const REQUESTED_BY_PRACTITIONER_IN_SCHEDULE = 'schedule';
    public const REQUESTED_BY_CLIENT = 'client';

    protected $fillable = [
        'schedule_id',
        'user_id',
        'new_schedule_id',
        'booking_id',
        'new_price_id',
        'comment',
        'old_location_displayed',
        'new_location_displayed',
        'old_location',
        'old_url',
        'new_url',
        'old_start_date',
        'new_start_date',
        'old_end_date',
        'new_end_date',
        'old_price_id',
        'requested_by',
        'noreply_sent',
    ];

    public function old_schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function new_schedule()
    {
        return $this->belongsTo(Schedule::class, 'new_schedule_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function old_price()
    {
        return $this->belongsTo(Price::class, 'old_price_id');
    }

    public function isAmendment(): bool
    {
        return $this->schedule_id === $this->new_schedule_id;
    }

    public static function getPractitionerRequestValues(): array
    {
        return [
            self::REQUESTED_BY_PRACTITIONER,
            self::REQUESTED_BY_PRACTITIONER_IN_SCHEDULE
        ];
    }
}
