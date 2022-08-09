<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Keeps data about transfers between the system manager (Holistify) and practitioners.
 * The data are used to track and return money to clients.
 * Tracking means that each payment into the system uses a transfer to pass
 * a part of money from the system account to a practitioner account.
 * To return money to clients transfers to practitioners must be returned to
 * the system to save the system money. Otherwise, refund will be done from
 * the system money instead of practitioners.
 *
 * @property int $amount
 * @property int $user_id
 * @property int $schedule_id
 * @property int $amount_original
 * @property string $status
 * @property string $currency
 * @property string $description
 * @property string $stripe_account_id
 * @property string $stripe_transfer_id
 * @property mixed|null $purchase_id
 * @property bool $is_installment
 */
class Transfer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_installment' => 'boolean',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
}
