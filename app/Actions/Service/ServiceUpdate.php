<?php

namespace App\Actions\Service;

use App\Http\Requests\Services\UpdateServiceRequest;
use App\Models\Service;

class ServiceUpdate extends ServiceAction
{
    public function execute(UpdateServiceRequest $request, Service $service): Service
    {
        unset($request['service_type_id']);

        if ($request->is_published) {
            $service->published_at = now();
        }

        $this->saveService($service, $request);

        return $service;
    }
}
