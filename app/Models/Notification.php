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
        'receiver_id',
        'old_address',
        'new_address',
        'price_id' ,
        'price_payed' ,
        'price_refunded',
        'read_at',
        'created_at',
        'updated_at' ,
    ];

    protected $casts = [
        'old_datetime'  => 'datetime',
        'new_datetime'  => 'datetime',
        'read_at'       => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];
}
