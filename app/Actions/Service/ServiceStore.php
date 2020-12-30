<?php

namespace App\Actions\Service;

use App\Http\Requests\Services\StoreServiceRequest;
use App\Models\Service;
use Stripe\StripeClient;

class ServiceStore extends ServiceAction {

    /**
     * @param \App\Http\Requests\Services\StoreServiceRequest $request
     * @return \App\Models\Service
     */
    public function execute(StoreServiceRequest $request, $stripeProduct): Service {
        $service = new Service();
        $service->stripe_id = $stripeProduct->id;
        $this->saveService($service, $request);
        return $service;
    }
}
