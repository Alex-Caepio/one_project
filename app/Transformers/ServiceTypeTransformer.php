<?php


namespace App\Transformers;


use App\Models\ServiceType;

class ServiceTypeTransformer extends Transformer
{
    public function transform(ServiceType $serviceType)
    {
        return [
            'id'           => $serviceType->id,
            'name'         => $serviceType->name,
            'created_at'   => $serviceType->created_at,
            'updated_at'   => $serviceType->updated_at,
        ];
    }

}
