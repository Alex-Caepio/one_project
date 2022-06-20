<?php


namespace App\Traits;

trait EventDates
{
    /**
     * @return string|null
     */
    public function getEventStartDate($event): ?string
    {
        if (isset($event->booking)) {
            return $event->booking->datetime_from;
        }

        if (isset($event->schedule)) {
            return $event->schedule->start_date;
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getEventEndDate($event): ?string
    {
        if (isset($event->booking)) {
            return $event->booking->datetime_to;
        }

        if (isset($event->schedule)) {
            return $event->schedule->end_date;
        }

        return null;
    }
}
