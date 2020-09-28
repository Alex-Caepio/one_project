<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;
    protected $fillable = ['name'];


    public function service_types()
    {
        return $this->belongsToMany(ServiceType::class);
    }

}

