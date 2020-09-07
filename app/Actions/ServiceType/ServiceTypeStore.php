<?php


namespace App\Actions\ServiceType;


use App\Http\Requests\Request;
use App\Models\ServiceType;

class ServiceTypeStore
{
    public function execute(Request $request)
    {
        $serviceType = new ServiceType();
        $serviceType->forceFill([
            'name' => $request->get('name'),
        ]);
        $serviceType->save();
    }

}
