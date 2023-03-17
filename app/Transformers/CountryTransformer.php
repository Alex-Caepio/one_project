<?php


namespace App\Transformers;


use App\Models\Country;

class CountryTransformer extends Transformer {

    public function transform(Country $country) {
        return [
            'id'         => $country->id,
            'iso'        => $country->iso,
            'name'       => $country->name,
            'nicename'   => $country->nicename,
            'iso3'       => $country->iso3,
            'numcode'    => $country->numcode,
            'phonecode'  => $country->phonecode,
            'created_at' => $country->created_at,
            'updated_at' => $country->updated_at,
        ];
    }
}
