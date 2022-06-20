<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomRate extends Model
{
    // TODO DELETE. MODEL IS UNUSED
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
