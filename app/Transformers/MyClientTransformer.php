<?php

namespace App\Transformers;

use App\Models\User;
use Carbon\Carbon;

class MyClientTransformer extends Transformer
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $myClient)
    {
        return [
            'id'                => $myClient->id,
            'name'              => "{$myClient->first_name} {$myClient->last_name}",
            'country'           => $myClient->country,
            'city'              => $myClient->city,
            'bookings'          => (int) $myClient->bookings,
            'live_bookings'     => (int) $myClient->live_bookings,
            'attended_bookings' => (int) $myClient->attended_bookings,
            'last_purchase'     => new Carbon($myClient->last_purchase),
            'last_service'      => new Carbon($myClient->last_service),
        ];
    }
}
