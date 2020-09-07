<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'amount',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
