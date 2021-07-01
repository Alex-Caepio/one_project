<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model {
    use HasFactory;

    protected $fillable = [
        'title',
        'client_id',
        'practitioner_id',
        'booking_id',
        'receiver_id',
        'old_address',
        'new_address',
        'price_id',
        'booking_id',
        'price_payed',
        'price_refunded',
        'read_at',
        'type',
        'old_datetime',
        'new_datetime',
        'datetime_from',
        'datetime_to',
    ];

    protected $casts = [
        'old_datetime'  => 'datetime',
        'new_datetime'  => 'datetime',
        'read_at'       => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'datetime_from' => 'datetime',
        'datetime_to'   => 'datetime',
    ];

    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function practitioner() {
        return $this->belongsTo(User::class, 'practitioner_id');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function price() {
        return $this->belongsTo(Price::class, 'price_id');
    }

    public function booking() {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

}
