<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionCode extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class,'user_promotion_code','user_id','promotion_code_id')->withTimeStamps();
    }
    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

}
