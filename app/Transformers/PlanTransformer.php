<?php


namespace App\Transformers;


use App\Models\Plan;

class PlanTransformer extends Transformer
{
    protected $availableIncludes = ['service_types'];

    public function transform(Plan $plan)
    {
        return [
            'id'                                    => $plan->id,
            'name'                                  => $plan->name,
            'description'                           => $plan->description,
            'image_url'                             => $plan->image_url,
            'stripe_id'                             => $plan->stripe_id,
            'price'                                 => $plan->price,
            'is_free'                               => (bool)$plan->is_free,
            'unlimited_bookings'                    => (bool)$plan->unlimited_bookings,
            'commission_on_sale'                    => $plan->commission_on_sale,
            'schedules_per_service'                 => $plan->schedules_per_service,
            'pricing_options_per_service'           => $plan->pricing_options_per_service,
            'pricing_options_per_service_unlimited' => (bool)$plan->pricing_options_per_service_unlimited,
            'list_paid_services'                    => (bool)$plan->list_paid_services,
            'list_free_services'                    => (bool)$plan->list_free_services,
            'take_deposits_and_instalment'          => (bool)$plan->take_deposits_and_instalment,
            'service_types'                         => $plan->service_types,
            'created_at'                            => $plan->created_at,
            'updated_at'                            => $plan->updated_at,
            'contact_clients_with_booking'          => (bool)$plan->contact_clients_with_booking,
            'market_to_clients'                     => (bool)$plan->market_to_clients,
            'client_reviews'                        => (bool)$plan->client_reviews,
            'article_publishing'                    => $plan->article_publishing,
            'article_publishing_unlimited'          => (bool)$plan->article_publishing_unlimited,
            'prioritised_business_profile_search'   => (bool)$plan->prioritised_business_profile_search,
            'prioritised_service_search'            => (bool)$plan->prioritised_service_search,
            'business_profile_page'                 => (bool)$plan->business_profile_page,
            'unique_web_address'                    => (bool)$plan->unique_web_address,
            'onboarding_support'                    => (bool)$plan->onboarding_support,
            'client_analytics'                      => (bool)$plan->client_analytics,
            'service_analytics'                     => (bool)$plan->service_analytics,
            'financial_analytics'                   => (bool)$plan->financial_analytics,
            'schedules_per_service_unlimited'       => (bool)$plan->schedules_per_service_unlimited,
            'amount_bookings'                       => $plan->amount_bookings,
            'discount_codes'                        => (bool)$plan->discount_codes,
            'order'                                 => $plan->order,
            'free_start_from'                       => $plan->free_start_from,
            'free_start_to'                         => $plan->free_start_to,
            'free_period_length'                    => $plan->free_period_length,
            'active_trial'                          => $plan->isActiveTrial()
        ];
    }

    public function includeServiceTypes(Plan $plan)
    {
        return $this->collectionOrNull($plan->service_types, new ServiceTypeTransformer());
    }
}
