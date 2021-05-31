<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property integer amount
 * @property string name
 * @property float commission_on_sale
 */
class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'image_url', 'stripe_id', 'price', 'is_free', 'unlimited_bookings', 'commission_on_sale',
        'schedules_per_service', 'pricing_options_per_service', 'list_paid_services',
        'list_free_services', 'take_deposits_and_instalment', 'description',
        'is_free', 'image_url', 'contact_clients_with_booking', 'market_to_clients',
        'client_reviews', 'article_publishing', 'article_publishing_unlimited',
        'prioritised_business_profile_search',
        'busines_profile_page', 'unique_web_address', 'onboarding_support',
        'client_analytics', 'service_analytics', 'financial_analytics', 'contact_clients_with_booking',
        'market_to_clients', 'client_reviews', 'article_publishing', 'article_publishing_unlimited',
        'prioritised_business_profile_search', 'prioritised_service_search', 'busines_profile_page',
        'unique_web_address', 'onboarding_support', 'client_analytics', 'service_analytics', 'financial_analytics',
        'schedules_per_service_unlimited', 'pricing_options_per_service_unlimited',
        'amount_bookings', 'discount_codes', 'order'
    ];

    protected $casts = [
        'article_publishing_unlimited'          => 'boolean',
        'list_free_services'                    => 'boolean',
        'list_paid_services'                    => 'boolean',
        'pricing_options_per_service_unlimited' => 'boolean',
        'unlimited_bookings'                    => 'boolean',
    ];

    public function service_types(): BelongsToMany
    {
        return $this->belongsToMany(ServiceType::class);
    }


}

