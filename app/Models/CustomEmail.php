<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomEmail extends Model
{
    use HasFactory;
    protected $fillable = ['logo','name','user_type','subject','text','delay'];
}
