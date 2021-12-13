<?php

namespace App\Transformers;

use App\Models\BookingView;
use Carbon\Carbon;

class MyClientTransformer extends Transformer
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(BookingView $myClient)
    {
        return [
            'id'                => $myClient->user->id,
            'name'              => "{$myClient->user->first_name} {$myClient->user->last_name}",
            'country'           => $myClient->user->country,
            'city'              => $myClient->user->city,
            'live_bookings'     => (int) $myClient->live_bookings,
            'last_purchase'     => new Carbon($myClient->last_purchase),
            'last_service'      => new Carbon($myClient->last_service),
        ];
    }
}
