<?php


namespace App\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;


class BookingFilters extends QueryFilter {

    private const STATUS_UPCOMING = 'upcoming';
    private const STATUS_RESCHEDULED = 'rescheduled';

    private bool $isStatusUpcoming;

    public function __construct(Request $request)
    {
        $this->defineIsStatusUpcoming($request);

        parent::__construct($request);
    }

    public function status(string $status): Builder {
        $status = strtolower($status);

        if ($this->hasUpcomingStatus()) {
            $statuses = [static::STATUS_UPCOMING, static::STATUS_RESCHEDULED];
        } else {
            $statuses = [$status];
        }

        return $this->builder->whereIn('status', $statuses);
    }

    public function hasUpcomingStatus(): bool
    {
        return $this->isStatusUpcoming;
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

    public function booking_reference(string $reference) {
        return $this->builder->where('reference', '=', $reference);
    }

    public function order_reference(string $reference) {
        return $this->builder->where('reference', '=', $reference);
    }

    public function service_type(string $serviceTypeId) {
        return $this->builder->whereHas('schedule.service', function($q) use ($serviceTypeId) {
            $q->whereIn('service_type_id', $this->paramToArray($serviceTypeId));
        });
    }

    public function isVirtual(string $isVirtual) {
        $isVirtual = strtolower($isVirtual);

        if ($isVirtual === 'virtual') {
            return $this->builder->whereHas('schedule', function($q) {
                $q->where('is_virtual', '=', true);
            });
        }

        if ($isVirtual === 'physical') {
            return $this->builder->whereHas('schedule', function($q) {
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

    protected function defineIsStatusUpcoming(Request $request): void
    {
        $this->isStatusUpcoming = $request->get('status') === static::STATUS_UPCOMING;
    }
}
