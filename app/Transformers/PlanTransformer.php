<?php


namespace App\Transformers;


use App\Models\Plan;

class PlanTransformer extends Transformer
{
    public function transform(Plan $plan)
    {
        return [
            'id'                             => $plan->id,
            'name'                           => $plan->name,
            'stripe_id'                      => $plan->stripe_id,
            'price'                          => $plan->price,
            'unlimited_bookings'             => $plan->unlimited_bookings,
            'commission_on_sale'             => $plan->commission_on_sale,
            'schedules_per_service'          => $plan->schedules_per_service,
            'pricing_options_per_service'    => $plan->pricing_options_per_service,
            'list_paid_services'             => $plan->list_paid_services,
            'list_free_services'             => $plan->list_free_services,
            'take_deposits_and_instalment'   => $plan->take_deposits_and_instalment,
            'service_types'                  => $plan->service_types,
            'created_at'                     => $plan->created_at,
            'updated_at'                     => $plan->updated_at,
        ];
    }
}
