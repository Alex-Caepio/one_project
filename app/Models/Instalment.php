<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int is_paid
 * @property int is_deposit
 * @property string $payment_amount
 * @property string|null $reference
 * @property string|null $subscription_id
 * @property-read Purchase $purchase
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

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('is_paid', 0);
    }
}
