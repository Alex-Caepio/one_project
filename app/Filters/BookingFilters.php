<?php


namespace App\Filters;

use Carbon\Carbon;

class BookingFilters extends QueryFilter {

//    public function search(string $searchQuery) {
//        $searchQuery = '%'.$searchQuery.'%';
//        return $this->builder->where(static function($query) use ($searchQuery) {
//            $query->whereHas('users', static function ($queryHas) use($searchQuery) {
//                $queryHas->where('email', 'LIKE', $searchQuery);
//            })->orWhere('reference', 'LIKE', $searchQuery);
//        });
//    }


    public function status(string $status) {
        $status = strtolower($status);
        if ($status === 'upcoming') {
            $statuses = ['upcoming', 'rescheduled'];
        } else {
            $statuses = [$status];
        }
        return $this->builder->whereIn('status', $statuses);
    }

    public function practitioner(int $id) {
        return $this->builder->where('practitioner_id', '=', $id);
    }

    public function datetime_from($datetimeFrom) {
        $date = Carbon::create($datetimeFrom)->toDateTimeString();
        return $this->builder->where('datetime_from', '>=', $date);
    }

    public function datetime_to($datetimeTo) {
        $date = Carbon::create($datetimeTo)->toDateTimeString();
        return $this->builder->where('datetime_from', '<=', $date);
    }

    public function bookingReference(string $reference) {
        return $this->builder->where('reference', '=', $reference);
    }

    public function serviceType(string $serviceTypeId) {
        return $this->builder->whereHas('schedule.service', function($q) use ($serviceTypeId) {
            $q->where('service_type_id', '=', $serviceTypeId);
        });
    }

    public function isVirtual(string $isVirtual) {
        $isVirtual = strtolower($isVirtual);

        if ($isVirtual === 'virtual') {
            return $this->builder->whereHas('schedule', function($q) use ($isVirtual) {
                $q->where('is_virtual', '=', true);
            });
        }

        if ($isVirtual === 'physical') {
            return $this->builder->whereHas('schedule', function($q) use ($isVirtual) {
                $q->where('is_virtual', '!=', true);
            });
        }
    }

    public function city(string $city) {
        return $this->builder->whereHas('schedule', function($q) use ($city) {
            $q->where('city', '=', strtolower($city));
        });
    }

    public function country(string $country) {
        return $this->builder->whereHas('schedule', function($q) use ($country) {
            $q->where('country', '=', strtolower($country));
        });
    }
}
