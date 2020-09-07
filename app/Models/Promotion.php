<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    public function promotion_codes()
    {
        return $this->hasMany(PromotionCode::class);
    }
}
