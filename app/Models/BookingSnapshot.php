<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property float commission_on_sale
 *
 * @property-read Booking $booking
 */
class BookingSnapshot extends Booking
{
    protected $fillable = [
        'user_id',
        'practitioner_id',
        'schedule_snapshot_id',
        'price_snapshot_id',
        'availability_id',
        'datetime_from',
        'datetime_to',
        'quantity',
        'cost',
        'promocode_snapshot_id',
        'created_at',
        'updated_at',
        'cancelled_at',
        'purchase_snapshot_id',
        'status',
        'amount',
        'discount',
        'is_fully_paid',
        'is_installment',
        'booking_id',
        'reference',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class)->withTrashed();
    }

    public function schedule()
    {
        return $this->belongsTo(ScheduleSnapshot::class, 'schedule_snapshot_id');
    }

    public function price()
    {
        return $this->belongsTo(PriceSnapshot::class, 'price_snapshot_id')->withTrashed();
    }

    public function purchase()
    {
        return $this->belongsTo(PurchaseSnapshot::class, 'purchase_snapshot_id');
    }

    public function reschedule_requests(): HasMany
    {
        return $this->hasMany(RescheduleRequest::class, 'booking_id');
    }

    public function delete()
    {
        if (isset($this->schedule->location)) {
            $this->schedule->location->forceDelete();
        }

        if (isset($this->purchase->promocode) && isset($this->purchase->promocode->promotionCode)) {
            $this->purchase->promocode->promotionCode->forceDelete();
        }

        if (isset($this->purchase->promocode)) {
            $this->purchase->promocode->forceDelete();
        }

        if (isset($this->schedule->service)) {
            $this->schedule->service->forceDelete();
        }

        if (isset($this->schedule)) {
            $this->schedule->forceDelete();
        }

        if (isset($this->purchase)) {
            $this->purchase->forceDelete();
        }

        if (isset($this->price)) {
            $this->price->forceDelete();
        }

        return parent::delete();
    }
}
