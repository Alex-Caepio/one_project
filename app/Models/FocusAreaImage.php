<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FocusAreaImage extends Model
{
    use HasFactory;
    public function focus_area()
    {
        return $this->belongsTo(FocusArea::class);
    }
}
