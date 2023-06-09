<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $amount
 * @property string $name
 * @property float $commission_on_sale
 * @property bool $article_publishing_unlimited
 * @property int $article_publishing
 */
class Plan extends Model
{
    use HasFactory;

    public const ORDER_OF_PLANS = [
        1 => 'Events',
        2 => 'Workshop',
        3 => 'Appointment',
        4 => 'Retreat',
        5 => 'Bespoke'
    ];

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'stripe_id',
        'price',
        'is_free',
        'unlimited_bookings',
        'commission_on_sale',
        'schedules_per_service',
        'pricing_options_per_service',
        'list_paid_services',
        'list_free_services',
        'take_deposits_and_instalment',
        'description',
        'is_free',
        'image_url',
        'contact_clients_with_booking',
        'market_to_clients',
        'client_reviews',
        'article_publishing',
        'article_publishing_unlimited',
        'prioritised_business_profile_search',
        'busines_profile_page',
        'unique_web_address',
        'onboarding_support',
        'client_analytics',
        'service_analytics',
        'financial_analytics',
        'contact_clients_with_booking',
        'market_to_clients',
        'client_reviews',
        'article_publishing',
        'article_publishing_unlimited',
        'prioritised_business_profile_search',
        'prioritised_service_search',
        'busines_profile_page',
        'unique_web_address',
        'onboarding_support',
        'client_analytics',
        'service_analytics',
        'financial_analytics',
        'schedules_per_service_unlimited',
        'pricing_options_per_service_unlimited',
        'amount_bookings',
        'discount_codes',
        'order',
        'free_start_from',
        'free_start_to',
        'free_period_length',
        'is_private'
    ];

    protected $casts = [
        'article_publishing_unlimited' => 'boolean',
        'list_free_services' => 'boolean',
        'list_paid_services' => 'boolean',
        'pricing_options_per_service_unlimited' => 'boolean',
        'unlimited_bookings' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'plan_id');
    }

    public function service_types(): BelongsToMany
    {
        return $this->belongsToMany(ServiceType::class);
    }

    public function isActiveTrial(): bool
    {
        if (!$this->free_start_from || !$this->free_start_to) {
            return false;
        }
        $nowDate = Carbon::now();

        return $nowDate >= Carbon::parse($this->free_start_from)
            && $nowDate <= Carbon::parse($this->free_start_to)
            && $this->free_period_length > 0;
    }

    public function isAllowedToPublishForNumberOfArticles(int $numberOfArticles): bool
    {
        if ($this->article_publishing_unlimited) {
            return true;
        }

        return $this->article_publishing > 0 && $numberOfArticles < $this->article_publishing;
    }
}
