<?php


namespace App\Filters;

use Carbon\Carbon;

class BookingFilters extends QueryFilter
{
    public function status(string $status)
    {
        $status = strtolower($status);

        if ($status === 'upcoming')
        {
            return $this->builder->where('datetime_from', '>', Carbon::now()->format('Y-m-d H:i:s'));
        }

        if ($status === 'completed'){
            return $this->builder->where('datetime_from', '<', Carbon::now()->format('Y-m-d H:i:s'));
        }

        if ($status === 'canceled')
        {
            return $this->builder->whereNotNull('deleted_at')->withTrashed();
        }

    }

    public function practitioner(int $id)
    {
        return $this->builder->where('practitioner_id', '=', $id);
    }

    public function datetime_from($datetimeFrom)
    {
        return $this->builder->where('datetime_from', '>=', $datetimeFrom);
    }

    public function datetime_to($datetimeTo)
    {
        return $this->builder->where('datetime_from', '<=', $datetimeTo);
    }

    public function bookingReference(string $reference)
    {
        return $this->builder->where('reference', '=', $reference);
    }

    public function serviceType(int $serviceTypeId)
    {
        return $this->builder->whereHas('schedule.service', function ($q) use($serviceTypeId)
        {
            $q->where('service_type_id', '=', $serviceTypeId);
        });
    }

    public function isVirtual(string $isVirtual)
    {
        $isVirtual = strtolower($isVirtual);

        if ($isVirtual === 'virtual') {
            return $this->builder->whereHas('schedule', function ($q) use ($isVirtual)
            {
                $q->where('is_virtual', '=', true);
            });
        }

        if ($isVirtual === 'physical') {
            return $this->builder->whereHas('schedule', function ($q) use ($isVirtual)
            {
                $q->where('is_virtual', '!=', true);
            });
        }
    }

    public function city(string $city)
    {
            return $this->builder->whereHas('schedule', function ($q) use ($city)
            {
                $q->where('city', '=', strtolower($city));
            });
    }

    public function country(string $country)
    {
        return $this->builder->whereHas('schedule', function ($q) use ($country)
        {
            $q->where('country', '=', strtolower($country));
        });
    }
}
