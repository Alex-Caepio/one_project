<?php

namespace App\Transformers;

use App\Models\GoogleCalendar;
use League\Fractal\Resource\Item;

class GoogleCalendarTransformer extends Transformer {

    protected $availableIncludes = ['user', 'timezone'];

    public function transform(GoogleCalendar $calendar) {
        return [
            'id'          => $calendar->id,
            'user_id'     => $calendar->user_id,
            'calendar_id' => $calendar->calendar_id,
            'timezone_id' => $calendar->timezone_id,
            'created_at'  => $calendar->created_at,
            'updated_at'  => $calendar->updated_at,
        ];
    }

    public function includeUser(GoogleCalendar $calendar): ?Item {
        return $this->itemOrNull($calendar->user, new UserTransformer());
    }

    public function includeTimezone(GoogleCalendar $calendar): ?Item {
        return $this->itemOrNull($calendar->timezone, new TimezoneTransformer());
    }

}
