<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property float $commission_on_sale
 * @property int $is_installment
 * @property int $is_fully_paid
 * @property string $status
 * @property int $user_id
 * @property int $practitioner_id
 * @property int $price_id
 * @property int $schedule_id
 * @property Carbon|string $datetime_from
 * @property Carbon|string $datetime_to
 *
 * @property-read Price $price
 * @property-read Purchase $purchase
 * @property-read Schedule $schedule
 * @property-read Schedule $schedule_with_trashed
 * @property-read BookingSnapshot $snapshot
 * @property-read User $practitioner
 * @property-read Collection|RescheduleRequest[] $reschedule_requests
 * @property-read RescheduleRequest|null $client_reschedule_request
 * @property-read RescheduleRequest|null $practitioner_reschedule_request
 */
class Booking extends Model
{
    use HasFactory, SoftDeletes;

    public const CANCELED_STATUS = 'canceled';
    public const COMPLETED_STATUS = 'completed';
    public const RESCHEDULED_STATUS = 'rescheduled';

    protected $observables = ['instalment_complete'];

    protected $fillable = [
        'user_id',
        'practitioner_id',
        'schedule_id',
        'price_id',
        'availability_id',
        'datetime_from',
        'datetime_to',
        'quantity',
        'cost',
        'reference',
        'promocode_id',
        'created_at',
        'updated_at',
        'cancelled_at',
        'purchase_id',
        'status',
        'amount',
        'discount',
        'is_fully_paid',
        'is_installment',
        'booking_snapshot_id',
    ];

    /**
     * fire event
     */
    public function installmentComplete(): void
    {
        $this->fireModelEvent('instalment_complete');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', self::getInactiveStatuses());
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeUncanceled(Builder $query): Builder
    {
        return $query->where('status', '!=', self::CANCELED_STATUS);
    }


    public function isActive(): bool
    {
        return !in_array($this->status, self::getInactiveStatuses());
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    public function practitioner()
    {
        return $this->belongsTo(User::class, 'practitioner_id', 'id')->withTrashed();
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function schedule_with_trashed()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id')->withTrashed();
    }

    public function snapshot()
    {
        return $this->belongsTo(BookingSnapshot::class, 'booking_snapshot_id');
    }

    public function price()
    {
        return $this->belongsTo(Price::class)->withTrashed();
    }

    public function schedule_availability()
    {
        return $this->belongsTo(ScheduleAvailability::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function cancellation(): HasOne
    {
        return $this->hasOne(Cancellation::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }

    public function reschedule_requests(): HasMany
    {
        return $this->hasMany(RescheduleRequest::class);
    }

    public function client_reschedule_request(): HasOne
    {
        return $this->hasOne(RescheduleRequest::class)->where('requested_by', RescheduleRequest::REQUESTED_BY_CLIENT);
    }

    public function practitioner_reschedule_request(): HasOne
    {
        return $this
            ->hasOne(RescheduleRequest::class)
            ->whereIn('requested_by', RescheduleRequest::getPractitionerRequestValues());
    }

    public static function getInactiveStatuses(): array
    {
        return [
            self::CANCELED_STATUS,
            self::COMPLETED_STATUS,
        ];
    }
}
