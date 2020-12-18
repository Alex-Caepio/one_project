<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instalments extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'user_id',
        'schedule_id',
        'price_id',
        'purchase_id',
        'payment_date',
        'is_paid',
        'payment_amount',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function price()
    {
        return $this->belongsTo(Price::class);
    }
    public function purchase()
    {
        return $this->belongsTo(Purchases::class);
    }
}
