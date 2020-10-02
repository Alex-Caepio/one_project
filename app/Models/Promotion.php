<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;
    public function promotion_codes()
    {
        return $this->hasMany(PromotionCode::class);
    }
}
