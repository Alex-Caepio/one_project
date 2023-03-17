<?php

namespace App\Actions\Schedule\TimeAvailability;

use Carbon\Carbon;

class BusyPeriod
{
    private Carbon $from;

    private Carbon $to;

    public function __construct(Carbon $from, Carbon $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function getFrom(): Carbon
    {
        return $this->from;
    }

    public function getTo(): Carbon
    {
        return $this->to;
    }

    public function doesIncludeTime(Carbon $time): bool
    {
        return $time->between($this->getFrom(), $this->getTo());
    }
}
