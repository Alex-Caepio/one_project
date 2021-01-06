<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model {
    use HasFactory;
    use softDeletes;

    protected $fillable = [
        'reference',
        'user_id',
        'schedule_id',
        'service_id',
        'price_id',
        'promocode_id',
        'price_original',
        'price',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_deposit',
        'deposit_amount'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo {
        return $this->belongsTo(Schedule::class);
    }

    public function service(): BelongsTo {
        return $this->belongsTo(Service::class);
    }

    public function promocode(): BelongsTo {
        return $this->belongsTo(PromotionCode::class);
    }

    public function price(): BelongsTo {
        return $this->belongsTo(Price::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters): Builder {
        return $filters->apply($builder);
    }

}
