<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed id
 * @property mixed stripe_id
 * @property mixed subscription_id
 * @property mixed deposit_amount
 */
class Purchase extends Model
{
    use HasFactory;
    use softDeletes;

    protected $fillable = [
        'reference',
        'user_id',
        'schedule_id',
        'service_id',
        'price_id',
        'promocode_id',
        'price_original',
        'price',
        'created_at',
        'updated_at',
        'deleted_at',
        'cancelled_at_subscription',
        'is_deposit',
        'deposit_amount',
        'stripe_id',
        'amount',
        'discount',
        'discount_applied'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at'                => 'datetime',
        'updated_at'                => 'datetime',
        'deleted_at'                => 'datetime',
        'cancelled_at_subscription' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function promocode(): BelongsTo
    {
        return $this->belongsTo(PromotionCode::class);
    }

    public function price(): BelongsTo
    {
        return $this->belongsTo(Price::class);
    }

    public function instalments(): HasMany
    {
        return $this->hasMany(Instalment::class);
    }

    public function cancellations(): HasMany
    {
        return $this->hasMany(Cancellation::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters): Builder
    {
        return $filters->apply($builder);
    }

}
