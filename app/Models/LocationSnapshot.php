<?php

namespace App\Models;


class LocationSnapshot extends Location
{
    protected $fillable = [
        'title',
        'location_id',
    ];


    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
