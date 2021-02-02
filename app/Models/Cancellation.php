<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cancellation extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_id',
        'purchase_id',
        'practitioner_id',
        'amount',
        'fee',
        'cancelled_by_client',
        'stripe_id'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id')
                    ->where('account_type', User::ACCOUNT_CLIENT);
    }

    public function practitioner(): BelongsTo {
        return $this->belongsTo(User::class, 'practitioner_id', 'id')
                    ->where('account_type', User::ACCOUNT_PRACTITIONER);
    }

    public function purchase(): BelongsTo {
        return $this->belongsTo(Purchase::class);
    }

    public function booking(): BelongsTo {
        return $this->belongsTo(Booking::class);
    }


}
