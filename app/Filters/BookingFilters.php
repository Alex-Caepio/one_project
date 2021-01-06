<?php


namespace App\Filters;

use Carbon\Carbon;

class BookingFilters extends QueryFilter
{
    public function status(string $status)
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
            return $this->builder->whereNotNull('deleted_at')->withTrashed();
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

    public function bookingReference(string $reference)
    {
        return $this->builder->where('reference', '=', $reference);
    }

    public function serviceType(int $service_type_id)
    {
        return $this->builder->whereHas('schedule.service', function ($q) use($service_type_id)
        {
            $q->where('service_type_id', '=', $service_type_id);
        });
    }

    public function isVirtual(string $is_virtual)
    {
        $is_virtual = strtolower($is_virtual);

        if ($is_virtual == 'virtual')
        {
            return $this->builder->whereHas('schedule', function ($q) use ($is_virtual)
            {
                $q->where('is_virtual', '=', true);
            });
        }
        elseif ($is_virtual == 'physical')
        {
            return $this->builder->whereHas('schedule', function ($q) use ($is_virtual)
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

    public function paymentMethod(string $payment)
    {
        $payment = strtolower($payment);

        if ($payment == 'deposit')
        {
            return $this->builder->whereHas('purchase', function ($q) use ($payment)
            {
                $q->where('is_deposit', '=', true);
            });
        }
        elseif ($payment == 'singlepayment')
        {
            return $this->builder->whereHas('purchase', function ($q) use ($payment)
            {
                $q->where('is_deposit', '=', false);
            });
        }
    }
}
