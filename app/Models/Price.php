<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'cost',
        'schedule_id',
        'name',
        'is_free',
        'available_till',
        'min_purchase',
        'number_available'
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
