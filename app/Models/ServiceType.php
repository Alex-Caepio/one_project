<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServiceType extends Model {
    use HasFactory;
    public $incrementing = false;
    protected $fillable = ['name'];

    public function plans() {
        return $this->belongsToMany(Plan::class);
    }

    public function services() {
        return $this->belongsToMany(Service::class);
    }
    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'service_type_user','service_type_id','user_id');
    }
}
