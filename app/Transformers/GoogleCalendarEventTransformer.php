<?php

namespace App\Transformers;

use App\Models\GoogleCalendar;
use League\Fractal\Resource\Item;

class GoogleCalendarEventTransformer extends Transformer {

    public function transform(\Google_Service_Calendar_Event $event) {
        return [
            'id'         => $event->getId(),
            'start_date' => $event->getStart()->dateTime,
            'end_date'   => $event->getEnd()->dateTime,
            'location'   => $event->getLocation(),
            'summary'    => $event->getSummary(),
            'clients'    => $this->getAttendiesArray($event->getAttendees())
        ];
    }


    /**
     * @param array $attendies
     * @return array
     */
    private function getAttendiesArray(array $attendies): array {
        $result = [];
        foreach ($attendies as $attendee) {
            $result[] = ['fullname' => $attendee->getDisplayName(), 'email' => $attendee->getEmail()];
        }
        return $result;
    }


}
