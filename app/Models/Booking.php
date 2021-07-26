<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property float commission_on_sale
 */
class Booking extends Model {
    use HasFactory, SoftDeletes;

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
        'promocode_id',
        'created_at',
        'updated_at',
        'cancelled_at',
        'purchase_id',
        'status',
        'amount',
        'discount',
        'is_installment'
    ];


    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder {
        return $query->whereNotIn('status', ['canceled', 'completed']);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUncanceled(Builder $query): Builder {
        return $query->where('status', '!=', 'canceled');
    }


    public function isActive(): bool {
        return !in_array($this->status, ['canceled', 'completed']);
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function practitioner() {
        return $this->belongsTo(User::class, 'practitioner_id', 'id');
    }

    public function schedule() {
        return $this->belongsTo(Schedule::class);
    }

    public function price() {
        return $this->belongsTo(Price::class);
    }

    public function schedule_availability() {
        return $this->belongsTo(ScheduleAvailability::class);
    }

    public function purchase() {
        return $this->belongsTo(Purchase::class);
    }

    public function cancellation(): HasOne {
        return $this->hasOne(Cancellation::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters) {
        return $filters->apply($builder);
    }

    public function reschedule_requests(): HasMany {
        return $this->hasMany(RescheduleRequest::class);
    }

    public function client_reschedule_request(): HasOne {
        return $this->hasOne(RescheduleRequest::class)->where('requested_by', 'client');
    }

    public function practitioner_reschedule_request(): HasOne {
        return $this->hasOne(RescheduleRequest::class)->where('requested_by', 'practitioner');
    }

}

