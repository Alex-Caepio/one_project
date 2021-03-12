<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomEmail extends Model {
    use HasFactory;

    protected $fillable = ['logo', 'logo_filename', 'name', 'user_type', 'subject', 'text', 'delay'];
}
