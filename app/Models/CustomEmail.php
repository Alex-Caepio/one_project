<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomEmail extends Model
{
    protected $fillable = ['logo','name','user_type','subject','text','delay'];
}
