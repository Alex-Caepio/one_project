<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Instalments are used to track payments by schedules (subscriptions) and deposits.
 * They Both work as Stripe subscriptions.
 *
 * @property int $id
 * @property int $is_paid
 * @property int $is_deposit
 * @property string $payment_amount
 * @property string|null $reference
 * @property string|null $subscription_id
 * @property Carbon|null $paid_at A real date when the instalment has been paid.
 * @property int|null $transfer_id A transfer ID. It is used to create its transfer reversal to return money.
 * @property string|null $stripe_invoice_id  A invoice ID. It is used to track the invoice and its actions:
*                                            payment, transaction.
 * @property string|null $stripe_charge_id A charge ID. It is used to create a refund of the payment.
 * @property string|null $stripe_payment_id A payment indent ID. It is used to create a refund of the payment.
 * @property string|null $stripe_refund_id A refund ID. It says that the instalment has been refunded.
 * @property Carbon|null $refunded_at A date when the refund has been done.
 * @property-read Purchase $purchase
 * @property-read Transfer|null $transfer
 *
 * @method static self|Builder query()
 * @method self|Builder unpaid() `scopeUnpaid()` Returns unpaid instalments.
 */
class Instalment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'purchase_id',
        'payment_date',
        'is_paid',
        'payment_amount',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_deposit',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'paid_at' => 'datetime',
        'is_deposit' => 'boolean',
        'is_paid' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function transfer(): HasOne
    {
        return $this->hasOne(Transfer::class);
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('is_paid', 0);
    }
}
