<?php


namespace App\Transformers;


use App\Models\Location;

class LocationTransformer extends Transformer
{
    public function transform(Location $location)
    {
        return [
            'id'           => $location->id,
            'title'        => $location->title,
            'created_at'   => $location->created_at,
            'updated_at'   => $location->updated_at,
        ];
    }
}
