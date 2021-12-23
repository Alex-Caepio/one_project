<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property float commission_on_sale
 */
class BookingSnapshot extends Booking {
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
        return $this->belongsTo(Booking::class);
    }

    public function schedule() {
        return $this->belongsTo(ScheduleSnapshot::class, 'schedule_snapshot_id');
    }

    public function price() {
        return $this->belongsTo(PriceSnapshot::class)->withTrashed();
    }

    public function purchase() {
        return $this->belongsTo(PurchaseSnapshot::class);
    }

    public function reschedule_requests(): HasMany {
        return $this->hasMany(RescheduleRequest::class, 'booking_id');
    }
}

