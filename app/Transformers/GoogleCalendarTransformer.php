<?php

namespace App\Transformers;

use App\Models\GoogleCalendar;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class GoogleCalendarTransformer extends Transformer {

    protected $availableIncludes = ['user', 'timezone', 'unavailabilities'];

    public function transform(GoogleCalendar $calendar) {
        return [
            'id'           => $calendar->id,
            'user_id'      => $calendar->user_id,
            'calendar_id'  => $calendar->calendar_id,
            'timezone_id'  => $calendar->timezone_id,
            'is_connected' => (bool)$calendar->is_connected,
            'created_at'   => $calendar->created_at,
            'updated_at'   => $calendar->updated_at,
        ];
    }

    public function includeUser(GoogleCalendar $calendar): ?Item {
        return $this->itemOrNull($calendar->user, new UserTransformer());
    }

    public function includeTimezone(GoogleCalendar $calendar): ?Item {
        return $this->itemOrNull($calendar->timezone, new TimezoneTransformer());
    }

    public function includeUnavailabilities(GoogleCalendar $calendar): ?Collection {
        return $this->collectionOrNull($calendar->unavailabilities, new UserUnavailabilitiesTransformer());
    }

}
