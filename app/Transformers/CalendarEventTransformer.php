<?php

namespace App\Transformers;

use App\Models\Booking;

class CalendarEventTransformer extends Transformer {

    public function transform(Booking $event) {
        return [
            'id'         => $event->id,
            'start_date' => $event->datetime_from,
            'end_date'   => $event->datetime_to,
            'location'   => $event->schedule->location_displayed,
            'summary'    => $event->schedule->title,
            //'clients'    => $this->getClientsArray($event->clients)
        ];
    }


    /**
     * @deprecated
     * @param null|array $clients
     * @return array
     */
    private function getClientsArray(?array $clients): array {
        $result = [];
        foreach ($clients as $client) {
            $result[] = ['fullname' => $client->getDisplayName(), 'email' => $client->getEmail()];
        }
        return $result;
    }


}
