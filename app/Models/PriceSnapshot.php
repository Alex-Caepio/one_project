<?php

namespace App\Models;


/**
 * Class PriceSnapshot
 *
 * @property Schedule schedule
 * @property int duration
 */
class PriceSnapshot extends Price {

    protected $fillable = [
        'cost',
        'schedule_snapshot_id',
        'name',
        'is_free',
        'available_till',
        'min_purchase',
        'number_available',
        'duration',
        'stripe_id',
        'price_id',
    ];

    public function price()
    {
        return $this->belongsTo(Price::class);
    }
}
