<?php

namespace App\Actions\Schedule;

use App\Models\Booking;
use App\Models\Price;
use App\Models\Schedule;
use App\Models\ScheduleAvailability;
use App\Models\ScheduleUnavailability;
use App\Models\UserUnavailabilities;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection;

class GetAvailableAppointmentTimeOnDate
{
    private const STEP_VALUE = 15;
    private const STEP_IN_MINUTS = self::STEP_VALUE.' minutes';

    private Price $price;

    private string $date;

    private string $timezone;

    public function execute(Price $price, string $date, string $timezone): array
    {
        $this->price = $price;
        $this->date = $date;
        $this->timezone = $timezone;

        /** @var Collection|ScheduleAvailability[] $availabilities */
        $availabilities = $this->getAvailabilitiesMatchingDate();
        $periods = $this->availabilitiesToCarbonPeriod($availabilities);
        $excludedTimes = $this->getExcludedTimes();

        $this->excludeTimes($periods, $excludedTimes);

        return $this->toTimes($periods, $timezone);
    }

    private function getSchedule(): Schedule
    {
        return $this->price->schedule;
    }

    private function getDuration(): int
    {
        return $this->price->duration;
    }

    private function getDate(): Carbon
    {
        return Carbon::createFromFormat('Y-m-d', $this->date, $this->timezone);
    }

    private function getStartOfDay(): Carbon
    {
        return $this->getDate()->startOfDay()->setTimezone('UTC');
    }

    private function getEndOfDay(): Carbon
    {
        return $this->getDate()->endOfDay()->setTimezone('UTC');
    }

    private function getMatchingDays($day): array
    {
        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday',];
        $weekends = ['saturday', 'sunday',];

        if (in_array($day, $weekends)) {
            return [$day, 'weekends', 'everyday'];
        }

        if (in_array($day, $weekdays)) {
            return [$day, 'weekdays', 'everyday'];
        }

        return [$day, 'everyday'];
    }

    /**
     * @return Collection|ScheduleAvailability[]
     */
    private function getAvailabilitiesMatchingDate(): Collection
    {
        $startDate = mb_strtolower($this->getStartOfDay()->isoFormat('dddd'));
        $endDate = mb_strtolower($this->getEndOfDay()->isoFormat('dddd'));

        $days = array_unique(array_merge($this->getMatchingDays($startDate), $this->getMatchingDays($endDate)));

        $availabilities = $this->getSchedule()->schedule_availabilities()->whereIn('days', $days)->get();

        return $availabilities;
    }

    /**
     * @param Collection|ScheduleAvailability[] $availabilities
     *
     * @return CarbonPeriod[]
     */
    private function availabilitiesToCarbonPeriod(Collection $availabilities): array
    {
        $periods = [];
        $reqPeriodStart = $this->getStartOfDay();
        $reqPeriodEnd = $this->getEndOfDay();
        $nowDatetime = $this->getNearestTime();

        foreach ($availabilities as $availability) {
            $baseDate = mb_strtolower($reqPeriodStart->isoFormat('dddd')) == $availability->days
                ? $reqPeriodStart
                : $reqPeriodEnd
            ;

            // Parse availabilities
            $availabilityStart = $this->createRoundedTime($baseDate, $availability->start_time);
            $availabilityEnd = $this->createRoundedTime($baseDate, $availability->end_time);

            if ($availabilityStart->greaterThanOrEqualTo($availabilityEnd)) {
                $availabilityEnd->addDay();
            }
            // End parse availabilities

            if ($nowDatetime->greaterThanOrEqualTo($availabilityStart)) {
                $availabilityStart = $nowDatetime;
            }

            if ($reqPeriodStart->greaterThanOrEqualTo($availabilityStart)) {
                $availabilityStart = $reqPeriodStart;
            }

            if ($availabilityEnd->greaterThanOrEqualTo($reqPeriodEnd)) {
                $availabilityEnd = $reqPeriodEnd;
            }

            if ($availabilityStart->greaterThanOrEqualTo($availabilityEnd)) {
                continue;
            }

            $periods[] = new CarbonPeriod($availabilityStart, self::STEP_IN_MINUTS, $availabilityEnd);
        }

        return $periods;
    }

    private function getNearestTime(): Carbon
    {
        return Carbon::now()
            ->addMinutes($this->getSchedule()->getNoticeTime())
            ->roundMinutes(self::STEP_VALUE, 'ceil')
        ;
    }

    private function createRoundedTime(Carbon $baseDate, string $time): Carbon
    {
        return Carbon::createFromFormat('Y-m-d H:i:s' , "{$baseDate->format('Y-m-d')} $time", 'UTC')
            ->roundMinutes(self::STEP_VALUE, 'ceil')
        ;
    }

    /**
     * @param CarbonPeriod[] $periods
     */
    private function excludeTimes($periods, array $excludedHours): void
    {
        foreach ($periods as $period) {
            /** @var Carbon[] $excludedHour */
            foreach ($excludedHours as $excludedHour) {
                $period->filter(fn (Carbon $date): bool => !$date->between($excludedHour['from'], $excludedHour['to']));
            }
        }
    }

    private function getExcludedTimes(): array
    {
        $scheduleIds = Schedule::where('service_id', $this->getSchedule()->service_id)->pluck('id');

        $startDate = $this->getStartOfDay();
        $endDate = $this->getEndOfDay();

        $excludedTimes = [];

        /** @var Booking[] $bookings */
        $bookings = Booking::whereIn('schedule_id', $scheduleIds)
            ->where('datetime_from', '>=', $startDate->toDateTimeString())
            ->where('datetime_from', '<=', $endDate->toDateTimeString())
            ->where('status', '!=', 'canceled')
            ->get();

        /** @var ScheduleUnavailability[] $unavailabilities */
        $unavailabilities = $this->getSchedule()->schedule_unavailabilities()
            ->where('start_date', '>=', $startDate->toDateTimeString())
            ->where('end_date', '<=', $endDate->toDateTimeString())
            ->get();

        /** @var ScheduleFreeze[] $frozenBookings */
        $frozenBookings = $this->getSchedule()->freezes()
            ->where('freeze_at', '>', Carbon::now()->subMinutes(15)->toDateTimeString())
            ->get();

        foreach ($bookings as $booking) {
            $excludedTimes[] = [
                'from' => Carbon::parse($booking->datetime_from)
                    ->subMinutes($this->getSchedule()->getBufferTime() + $this->getDuration())
                    ->addSecond(),
                'to' => Carbon::parse($booking->datetime_to)
                    ->addMinutes($this->getSchedule()->getBufferTime())
                    ->subSecond()
            ];
        }

        foreach ($unavailabilities as $unavailability) {
            $excludedTimes[] = [
                'from' => Carbon::parse($unavailability->start_date)->subMinutes($this->getDuration())->addSecond(),
                'to' => Carbon::parse($unavailability->end_date)->subSecond()
            ];
        }

        /** @var UserUnavailabilities[] $globalUnavailabilities */
        $globalUnavailabilities = UserUnavailabilities::query()
            ->where('practitioner_id', $this->getSchedule()->service->user_id)
            ->get()
        ;

        foreach ($globalUnavailabilities as $unavailability) {
            $excludedTimes[] = [
                'from' => Carbon::parse($unavailability->start_date)->subMinutes($this->getDuration())->addSecond(),
                'to' => Carbon::parse($unavailability->end_date)->subSecond()
            ];
        }

        foreach ($frozenBookings as $frozenBooking) {
            $excludedTimes[] = [
                'from' => Carbon::parse($frozenBooking->freeze_at)->subMinutes($this->getDuration())->addSecond(),
                'to' => Carbon::parse($frozenBooking->freeze_at)->addMinutes(15)->subSecond()
            ];
        }

        return $excludedTimes;
    }

    /**
     * Converts periods to times with user's timezone.
     *
     * @param CarbonPeriod[] $periods
     *
     * @return string[]
     */
    private function toTimes(array $periods, string $timezone): array
    {
        $flatTimes = [];

        foreach ($periods as $period) {
            foreach ($period as $time) {
                $flatTimes[] = $time->setTimezone($timezone)->format('H:i');
            }
        }

        sort($flatTimes, SORT_NATURAL);

        return array_unique($flatTimes);
    }
}
