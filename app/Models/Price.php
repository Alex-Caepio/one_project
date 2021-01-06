<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Price extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cost',
        'schedule_id',
        'name',
        'is_free',
        'available_till',
        'min_purchase',
        'number_available',
        'stripe_id'
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
