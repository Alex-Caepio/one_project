<?php

namespace App\Actions\Service;

use App\Http\Requests\Services\StoreServiceRequest;
use App\Models\Service;

class ServiceStore extends ServiceAction {

    /**
     * @param \App\Http\Requests\Services\StoreServiceRequest $request
     * @return \App\Models\Service
     */
    public function execute(StoreServiceRequest $request): Service {
        $service = new Service();
        $this->saveService($service, $request);
        return $service;
    }
}
