<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class PurchaseSnapshot
 *
 * @property mixed id
 * @property mixed stripe_id
 * @property mixed subscription_id
 * @property mixed deposit_amount
 */
class PurchaseSnapshot extends Purchase
{
    protected $fillable = [
        'reference',
        'user_id',
        'schedule_snapshot_id',
        'service_snapshot_id',
        'price_snapshot_id',
        'promocode_snapshot_id',
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
        'discount_applied',
        'purchase_id',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ScheduleSnapshot::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(ServiceSnapshot::class, 'service_snapshot_id');
    }

    public function promocode(): BelongsTo
    {
        return $this->belongsTo(PromotionCodeSnapshot::class);
    }

    public function price(): BelongsTo
    {
        return $this->belongsTo(PriceSnapshot::class);
    }

    public function instalments(): HasMany
    {
        return $this->purchase->instalments();
    }
}
