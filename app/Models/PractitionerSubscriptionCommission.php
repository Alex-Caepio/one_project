<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PractitionerSubscriptionCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rate',
        'date_from',
        'date_to',
        'is_dateless',
        'discount_id',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
