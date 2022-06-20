<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplineImage extends Model
{
    public function disciplines()
    {
        return $this->belongsTo(Discipline::class);
    }
}
