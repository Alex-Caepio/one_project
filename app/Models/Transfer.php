<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int amount
 * @property int user_id
 * @property int schedule_id
 * @property int amount_original
 * @property string status
 * @property string currency
 * @property string description
 * @property string stripe_account_id
 */
class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [];

}

