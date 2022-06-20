<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $coupon_id
 * @property string $subscription_id
 * @property int $rate
 * @property string $duration_type
 * @property int $duration_in_months
 * @property-read User $user
 */
class PractitionerSubscriptionDiscount extends Model
{
    use HasFactory;

    public const FOREVER_SUBCRIPTION_TYPE = 'forever';
    public const REPEATING_SUBSCRIPTION_TYPE = 'repeating';
    public const ONCE_SUBSCRIPTION_TYPE = 'once';

    protected $fillable = [
        'user_id',
        'coupon_id',
        'subscription_id',
        'rate',
        'duration_type',
        'duration_in_months',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hasDifferences(
        string $durationType,
        int $rate,
        int $durationInMonths = null
    ): bool {
        return $this->duration_type !== $durationType
            || $this->rate !== $rate
            || $this->duration_in_months !== $durationInMonths;
    }

    public static function getSubscriptionTypes(): array
    {
        return [
            self::FOREVER_SUBCRIPTION_TYPE,
            self::REPEATING_SUBSCRIPTION_TYPE,
            self::ONCE_SUBSCRIPTION_TYPE,
        ];
    }
}
