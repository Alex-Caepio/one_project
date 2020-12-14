<?php


namespace App\Filters;

use Carbon\Carbon;

class BookingFilters extends QueryFilter
{
    public function status($status)
    {
        if ($status == 'upcoming')
        {
            return $this->builder->where('datetime_from' > Carbon::now());
        }

        if ($status == 'completed'){
            return $this->builder->where('datetime_from' > Carbon::now());
        }

        if ($status == 'canceled')
        {
            return $this->builder->where('deleted_at' !== null);
        }

    }
}
