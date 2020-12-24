<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PractitionerCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'practitioner_id',
        'rate',
        'date_from',
        'date_to',
        'is_dateless',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
