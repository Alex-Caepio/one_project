<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceType extends Model
{
    use HasFactory;
    public function plans()
    {
        return $this->belongsToMany(Plan::class);
    }
    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
}
