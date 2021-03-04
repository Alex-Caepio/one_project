<?php


namespace App\Actions\Service;

use App\Http\Requests\Services\StoreServiceRequest;
use App\Models\Service;

class ServiceUpdate extends ServiceAction {

    /**
     * @param \App\Http\Requests\Services\StoreServiceRequest $request
     * @param \App\Models\Service $service
     * @return \App\Models\Service
     */
    public function execute(StoreServiceRequest $request, Service $service): Service {
        $request->has('service_type') 
            ?  $this->saveService($service, $request->except(['service_type']))
            :  $this->saveService($service, $data);

        return $service;
    }

}
