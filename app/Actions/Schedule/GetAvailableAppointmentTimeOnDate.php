<?php

namespace App\Actions\Schedule;

use App\Actions\Schedule\TimeAvailability\AvailablePeriodCollection;
use App\Actions\Schedule\TimeAvailability\BusyPeriodCollection;
use App\Models\Booking;
use App\Models\Price;
use App\Models\Schedule;
use App\Models\ScheduleAvailability;
use App\Models\ScheduleFreeze;
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
        $busyPeriods = $this->getBusyPeriods();

        $this->addBusyPeriodByEndOfWorkingDay($busyPeriods, $availabilities);

        return $periods
            ->excludeBusyPeriods($busyPeriods)
            ->toTimes($timezone)
        ;
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

    /**
     * @param Collection|ScheduleAvailability[] $availabilities
     *
     * @return Carbon
     */
    private function detectEndOfWorkingDay(Collection $availabilities)
    {
        $startDate = mb_strtolower($this->getStartOfDay()->isoFormat('dddd'));
        $endDate = mb_strtolower($this->getEndOfDay()->isoFormat('dddd'));

        $ranges = $availabilities->filter(fn (ScheduleAvailability $item): bool => $item->days === $endDate);

        if ($ranges->isEmpty()) {
            $ranges = $availabilities->filter(fn (ScheduleAvailability $item): bool => $item->days === $startDate);
        }

        /** @var Collection|Carbon[] $times */
        $times = $ranges->map(function (ScheduleAvailability $item): Carbon {
            return $this->createTime($this->getDate(), $item->end_time);
        });

        $max = $times->first();

        foreach ($times as $value) {
            $max = $value->greaterThanOrEqualTo($max) ? $value : $max;
        }

        return $max;
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
     * @return AvailablePeriodCollection
     */
    private function availabilitiesToCarbonPeriod(Collection $availabilities): AvailablePeriodCollection
    {
        $periods = new AvailablePeriodCollection();
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

            $periods->push(new CarbonPeriod($availabilityStart, self::STEP_IN_MINUTS, $availabilityEnd));
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

    private function createTime(Carbon $date, string $time): Carbon
    {
        return Carbon::createFromFormat('Y-m-d H:i:s' , "{$date->format('Y-m-d')} $time", 'UTC');
    }

    private function createRoundedTime(Carbon $date, string $time): Carbon
    {
        return $this->createTime($date, $time)->roundMinutes(self::STEP_VALUE, 'ceil');
    }

    private function getBusyPeriods(): BusyPeriodCollection
    {
        return $this->getBusyPeriodsByBookings()
            ->merge($this->getBusyPeriodsByScheduleUnavailabilities())
            ->merge($this->getBusyPeriodsByScheduleFreezes())
            ->merge($this->getBusyPeriodsByUserUnavailabilities())
        ;
    }

    private function getBusyPeriodsByBookings(): BusyPeriodCollection
    {
        $busyPeriods = new BusyPeriodCollection();

        $scheduleIds = Schedule::where('service_id', $this->getSchedule()->service_id)->pluck('id');

        /** @var Booking[] $bookings */
        $bookings = Booking::whereIn('schedule_id', $scheduleIds)
            ->where('datetime_from', '>=', $this->getStartOfDay()->toDateTimeString())
            ->where('datetime_from', '<=', $this->getEndOfDay()->toDateTimeString())
            ->where('status', '!=', 'canceled')
            ->get()
        ;

        foreach ($bookings as $booking) {
            $busyPeriods->addFromTimes(
                $booking->datetime_from
                    ->subMinutes($this->getSchedule()->getBufferTime() + $this->getDuration())
                    ->addSecond(),
                $booking->datetime_to
                    ->addMinutes($this->getSchedule()->getBufferTime())
                    ->subSecond()
            );
        }

        return $busyPeriods;
    }

    private function getBusyPeriodsByScheduleUnavailabilities(): BusyPeriodCollection
    {
        $busyPeriods = new BusyPeriodCollection();

        /** @var ScheduleUnavailability[] $unavailabilities */
        $unavailabilities = $this->getSchedule()
            ->schedule_unavailabilities()
            ->where('start_date', '>=', $this->getStartOfDay()->toDateTimeString())
            ->where('end_date', '<=', $this->getEndOfDay()->toDateTimeString())
            ->get()
        ;

        foreach ($unavailabilities as $unavailability) {
            $busyPeriods->addFromTimes(
                $unavailability->start_date->subMinutes($this->getDuration())->addSecond(),
                $unavailability->end_date->subSecond()
            );
        }

        return $busyPeriods;
    }

    private function getBusyPeriodsByScheduleFreezes(): BusyPeriodCollection
    {
        $busyPeriods = new BusyPeriodCollection();

        /** @var ScheduleFreeze[] $frozenBookings */
        $frozenBookings = $this->getSchedule()
            ->freezes()
            ->where('freeze_at', '>', Carbon::now()->subMinutes(15)->toDateTimeString())
            ->get()
        ;

        foreach ($frozenBookings as $frozenBooking) {
            $busyPeriods->addFromTimes(
                $frozenBooking->freeze_at->subMinutes($this->getDuration())->addSecond(),
                $frozenBooking->freeze_at->addMinutes(15)->subSecond()
            );
        }

        return $busyPeriods;
    }

    private function getBusyPeriodsByUserUnavailabilities(): BusyPeriodCollection
    {
        $busyPeriods = new BusyPeriodCollection();

        /** @var UserUnavailabilities[] $unavailabilities */
        $unavailabilities = UserUnavailabilities::query()
            ->where('practitioner_id', $this->getSchedule()->service->user_id)
            ->get()
        ;

        foreach ($unavailabilities as $unavailability) {
            $busyPeriods->addFromTimes(
                $unavailability->start_date->subMinutes($this->getDuration())->addSecond(),
                $unavailability->end_date->subSecond()
            );
        }

        return $busyPeriods;
    }

    /**
     * @param Collection|ScheduleAvailability[] $availabilities
     */
    private function addBusyPeriodByEndOfWorkingDay(
        BusyPeriodCollection $busyPeriods,
        Collection $availabilities
    ): BusyPeriodCollection {
        $endOfWorkingDay = $this->detectEndOfWorkingDay($availabilities);

        return $busyPeriods->addFromTimes(
            $endOfWorkingDay->clone()->subMinutes($this->getDuration())->addSecond(),
            $endOfWorkingDay
        );
    }
}
