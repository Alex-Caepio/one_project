<?php

namespace App\Actions\Auth;

class GetUsersPermissions
{
    public function execute($user){

        $permissions = [];
        $plan = $user->plan;

        if($user->is_admin)
        {
            $permissions[] = 'admin_panel:view';
        }

        if(!$plan)
        {
             return $permissions;
        }

        if($plan->list_paid_services || $plan->list_free_services )
        {
            $permissions[] = 'service:create';
        }
        if($plan->list_free_services)
        {
            $permissions[] = 'service:create_free';
        }
        if($plan->list_paid_services)
        {
            $permissions[] = 'service:create_paid';
        }
        if($plan->take_deposits_and_instalment)
        {
            $permissions[] = 'service:accept_deposit';
        }
        if($plan->contact_clients_with_booking)
        {
            $permissions[] = 'service:contact_clients';
        }
        if($plan->article_publishing)
        {
            $permissions[] = 'article:publish';
        }
        if($plan->business_profile_page)
        {
            $permissions[] = 'profile:business_page';
        }

        return $permissions;
   }
}
