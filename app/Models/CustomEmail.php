<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomEmail extends Model {
    use HasFactory;

    public const CLIENT_EMAIL = 'client';
    public const PRACTITIONER_EMAIL = 'practitioner';
    public const ALL_EMAIL = 'all';


    protected $fillable = [
        'logo',
        'logo_filename',
        'subject',
        'text',
        'delay',
        'from_email',
        'from_title'
    ];
}
