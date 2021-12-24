<?php

namespace App\Transformers;

use App\Models\Booking;

class CalendarEventTransformer extends Transformer
{

    public function transform(Booking $event): array
    {
        return [
            'id' => $event->id,
            'start_date' => $event->datetime_from,
            'end_date' => $event->datetime_to,
            'location' => $event->schedule->location_displayed,
            'summary' => $event->schedule->title,
            'resource' => [
                'schedule_id' => $event->schedule_id,
                'service_id' => $event->schedule->service_id,
                'user' => [
                    'first_name' => $event->user->first_name,
                    'last_name' => $event->user->last_name,
                ]
            ]
        ];
    }

}
