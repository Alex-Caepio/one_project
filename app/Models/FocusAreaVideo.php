<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FocusAreaVideo extends Model
{
    use HasFactory;
    public function disciplines()
    {
        return $this->belongsTo(Discipline::class);
    }
}
