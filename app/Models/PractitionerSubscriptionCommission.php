<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PractitionerSubscriptionCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rate',
        'date_from',
        'date_to',
        'is_dateless',
        'created_at',
        'updated_at',
        'stripe_coupon_id',
        'subscription_schedule_id',
    ];

    protected $casts = [
        'date_from' => 'datetime',
        'date_to' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
