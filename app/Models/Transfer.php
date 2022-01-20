<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int amount
 * @property int user_id
 * @property int schedule_id
 * @property int amount_original
 * @property string status
 * @property string currency
 * @property string description
 * @property string stripe_account_id
 * @property string stripe_transfer_id
 * @property mixed|null $purchase_id
 */
class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function purchase(): BelongsTo {
        return $this->belongsTo(Purchase::class);
    }


}
