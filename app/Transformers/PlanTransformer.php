<?php


namespace App\Transformers;


use App\Models\Plan;

class PlanTransformer extends Transformer
{
    public function transform(Plan $plan)
    {
        return [
            'id'                                    => $plan->id,
            'name'                                  => $plan->name,
            'description'                           => $plan->description,
            'image_url'                             => $plan->image_url,
            'stripe_id'                             => $plan->stripe_id,
            'price'                                 => $plan->price,
            'is_free'                               => $plan->is_free,
            'unlimited_bookings'                    => $plan->unlimited_bookings,
            'commission_on_sale'                    => $plan->commission_on_sale,
            'schedules_per_service'                 => $plan->schedules_per_service,
            'pricing_options_per_service'           => $plan->pricing_options_per_service,
            'list_paid_services'                    => $plan->list_paid_services,
            'list_free_services'                    => $plan->list_free_services,
            'take_deposits_and_instalment'          => $plan->take_deposits_and_instalment,
            'service_types'                         => $plan->service_types,
            'created_at'                            => $plan->created_at,
            'updated_at'                            => $plan->updated_at,
            'contact_clients_with_booking'          => $plan->contact_clients_with_booking,
            'market_to_clients'                     => $plan->market_to_clients,
            'client_reviews'                        => $plan->client_reviews,
            'article_publishing'                    => $plan->article_publishing,
            'article_publishing_unlimited'          => $plan->article_publishing_unlimited,
            'prioritised_business_profile_search'   => $plan->prioritised_business_profile_search,
            'prioritised_serivce_search'            => $plan->prioritised_serivce_search,
            'busines_profile_page'                  => $plan->busines_profile_page,
            'unique_web_address'                    => $plan->unique_web_address,
            'onboarding_support'                    => $plan->onboarding_support,
            'client_analytics'                      => $plan->client_analytics,
            'service_analytics'                     => $plan->service_analytics,
            'financial_analytics'                   => $plan->financial_analytics,

        ];
    }
}
