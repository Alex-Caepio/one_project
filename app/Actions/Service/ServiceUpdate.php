<?php


namespace App\Actions\Service;

use App\Http\Requests\Services\UpdateServiceRequest;
use App\Models\Service;

class ServiceUpdate extends ServiceAction {

    /**
     * @param \App\Http\Requests\Services\StoreServiceRequest $request
     * @param \App\Models\Service $service
     * @return \App\Models\Service
     */
    public function execute(UpdateServiceRequest $request, Service $service): Service {
        unset($request['service_type_id']);
        $this->saveService($service, $request);

        return $service;
    }

}
