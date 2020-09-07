<?php

namespace App\Transformers\Serializers;

use League\Fractal\Serializer\ArraySerializer as BaseArraySerializer;

class ArraySerializer extends BaseArraySerializer
{
    /**
     * Serialize a collection to a plain array.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function collection($resourceKey, array $data): array
    {
        return $data;
    }
}
