<?php


namespace App\Actions\Service;


use App\Http\Requests\Services\UpdateServiceRequest;
use App\Models\Service;

class ServiceUpdate extends ServiceAction {

    /**
     * @param \App\Http\Requests\Services\UpdateServiceRequest $request
     * @param \App\Models\Service $service
     * @return \App\Models\Service
     */
    public function execute(UpdateServiceRequest $request, Service $service): Service {
        $this->saveService($service, $request);
        return $service;
    }

}
