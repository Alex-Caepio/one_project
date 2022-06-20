<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingView extends Model
{
    protected $table = 'view_booking';

    public const LIVE_BOOKING_STATUS = ['upcoming', 'rescheduled'];
    public const BESPOKE_SERVICE_VALUE = Service::TYPE_BESPOKE;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }
}
