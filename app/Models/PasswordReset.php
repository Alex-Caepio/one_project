<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model {
    use HasFactory;

    /**
     *
     */
    const UPDATED_AT = null;

    /**
     * @var string[]
     */
    protected $fillable = ['email', 'token'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class, 'email', 'email');
    }

}
