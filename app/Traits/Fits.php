<?php


namespace App\Traits;

use Carbon\Carbon;

trait Fits {

    public function fits(string $datetime): bool {
        $time = new Carbon($datetime);
        $timeStart = new Carbon($this->start_date);
        $timeEnd = new Carbon($this->end_date);

        return $time->between($timeStart, $timeEnd);
    }

}
