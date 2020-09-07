<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name'];


    public function service_types()
    {
        return $this->belongsToMany(ServiceType::class);
    }

}

