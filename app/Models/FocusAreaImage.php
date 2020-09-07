<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FocusAreaImage extends Model
{
    public function focus_area()
    {
        return $this->belongsTo(FocusArea::class);
    }
}
