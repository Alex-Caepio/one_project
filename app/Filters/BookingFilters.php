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
            return $this->builder->where('datetime_from', '<', Carbon::now()->format('Y-m-d H:i:s'));
        }

        if ($status == 'canceled')
        {
            return $this->builder->whereNotNull('deleted_at');
        }

    }

    public function practitioner(int $id)
    {
        return $this->builder->whereHas('schedule.service', function ($q) use($id)
        {
           $q->where('user_id', '=', $id);
        });
    }

    public function datetime_from($datetime_from)
    {
        return $this->builder->where('datetime_from', '>=', $datetime_from);
    }

    public function datetime_to($datetime_to)
    {
        return $this->builder->where('datetime_from', '<=', $datetime_to);
    }
}
