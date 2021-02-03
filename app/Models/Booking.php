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
class Booking extends Model
{
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
        'purchase_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function practitioner()
    {
        return $this->belongsTo(User::class, 'practitioner_id', 'id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function price()
    {
        return $this->belongsTo(Price::class);
    }

    public function schedule_availability()
    {
        return $this->belongsTo(ScheduleAvailability::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function cancellation(): HasOne {
        return $this->hasOne(Cancellation::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }
}

