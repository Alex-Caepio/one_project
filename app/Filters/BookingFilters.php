<?php


namespace App\Filters;

use Carbon\Carbon;

class BookingFilters extends QueryFilter
{
    public function status($status)
    {
        $status = strtolower($status);

        if ($status == 'upcoming')
        {
            return $this->builder->where('datetime_from', '>', Carbon::now()->format('Y-m-d H:i:s'));
        }

        if ($status == 'completed'){
            return $this->builder->where('datetime_from' ,'<', Carbon::now()->format('Y-m-d H:i:s'));
        }

        if ($status == 'canceled')
        {
            return $this->builder->whereNotNull('deleted_at');
        }

    }
}
