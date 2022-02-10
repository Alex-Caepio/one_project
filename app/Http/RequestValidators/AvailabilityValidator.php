<?php

namespace App\Http\RequestValidators;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\ScheduleAvailability;
use App\Models\ScheduleUnavailability;
use App\Models\Service;
use App\Models\UserUnavailabilities;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Validator;

class AvailabilityValidator implements RequestValidatorInterface
{
    private const TIMESLOT_IS_UNAVAILABLE = 'This timeslot is unavailable';

    private const TIMESLOT_IS_BOOKED = 'This time slot is already booked';

    private const UNAVAILABLE_BY_PRACTITIONER = 'That date marked as unavailable by practitioner';

    private const NO_AVAILABLE_TIME_SLOT  = 'No available time slot for selected appointment';

    private Schedule $schedule;

    /**
     * From these dates and times.
     *
     * @var string[]
     */
    private $datetimes = [];

    /**
     * @var Collection|ScheduleAvailability[]
     */
    private Collection $scheduleAvailabilities;
    /**
     * @var Collection|ScheduleUnavailability[]
     */
    private Collection $scheduleUnavailabilities;

    /**
     * @var Collection|UserUnavailabilities[]
     */
    private Collection $userUnavailabilities;

    private Validator $validator;

    public function setSchedule(Schedule $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * @param string[] $date
     */
    public function setDatetimes(array $availabilities): self
    {
        $this->datetimes = $availabilities;

        return $this;
    }

    public function validate(Validator $validator): Validator
    {
        $this->validator = $validator;
        $this->scheduleAvailabilities = $this->schedule->schedule_availabilities;
        $this->scheduleUnavailabilities = $this->schedule->schedule_unavailabilities;
        $this->userUnavailabilities = UserUnavailabilities::query()
            ->where('practitioner_id', $this->schedule->service->user_id)
            ->get();

        if (!$this->scheduleAvailabilities->count()) {
            return $validator;
        }

        foreach ($this->datetimes as $index => $availabilityRequest) {
            if (!$this->validateAvailability($index, $availabilityRequest)) {
                return $validator;
            }
        }

        return $validator;
    }

    private function validateAvailability(int $index, string $from): bool
    {
        if (Carbon::parse($from) <= Carbon::now()) {
            $this->validator->errors()->add("availabilities.$index.datetime_from", self::TIMESLOT_IS_UNAVAILABLE);

            return false;
        }

        if ($this->schedule->service->service_type_id === Service::TYPE_APPOINTMENT) {
            $alreadyBookedAppointment = Booking::query()
                ->where('practitioner_id', $this->schedule->service->user_id)
                ->where('datetime_from', $from)
                ->whereHas('schedule.service', static function ($serviceQuery) {
                    $serviceQuery->where('services.service_type_id', Service::TYPE_APPOINTMENT);
                })
                ->uncanceled()
                ->exists();

            if ($alreadyBookedAppointment) {
                $this->validator->errors()->add("availabilities.$index.datetime_from", self::TIMESLOT_IS_BOOKED);

                return false;
            }
        }

        if (
            $this->userUnavailabilities->count()
            && $this->withinUnavailabilities($from, $this->userUnavailabilities)
        ) {
            $this->validator->errors()->add("availabilities.$index.datetime_from", self::UNAVAILABLE_BY_PRACTITIONER);

            return false;
        }

        if (
            $this->scheduleUnavailabilities->count()
            && $this->withinUnavailabilities($from, $this->scheduleUnavailabilities)
        ) {
            $this->validator->errors()->add("availabilities.$index.datetime_from", self::UNAVAILABLE_BY_PRACTITIONER);

            return false;
        }

        if (!$this->fits($from, $this->scheduleAvailabilities)) {
            $this->validator->errors()->add("availabilities.$index.datetime_from", self::NO_AVAILABLE_TIME_SLOT);

            return false;
        }

        return true;
    }

    private function withinUnavailabilities(string $datetime, Collection $unavailabilities): bool
    {
        foreach ($unavailabilities as $unavailability) {
            /** @var ScheduleUnavailability $unavailability */
            /** @var UserUnavailabilities $unavailability */
            if ($unavailability->fits($datetime)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param ScheduleAvailability[] $availabilities
     */
    private function fits(string $datetime, $availabilities): bool
    {
        foreach ($availabilities as $availability) {
            if ($availability->fits($datetime)) {
                return true;
            }
        }

        return false;
    }
}
