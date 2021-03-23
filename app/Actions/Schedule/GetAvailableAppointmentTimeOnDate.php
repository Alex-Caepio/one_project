<?php

namespace App\Actions\Schedule;


use App\Models\Schedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class GetAvailableAppointmentTimeOnDate
{
    public const STEP = '15 minutes';

    public function execute(Schedule $schedule, $date)
    {
        $availabilities = $this->getAvailabilitiesMatchingDate($date, $schedule);
        $periods        = $this->availabilitiesToCarbonPeriod($date, $availabilities);
        $excludedTimes  = $this->getExcludedTimes($schedule, $date, $schedule->buffer_time);

        $this->excludeTimes($periods, $excludedTimes);

        $flatTimes  = $this->toTimes($periods);
        return array_unique($flatTimes);
    }

    protected function getMatchingDays($day)
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

    protected function getAvailabilitiesMatchingDate($date, $schedule)
    {
        $convertedDay = mb_strtolower(Carbon::parse($date)->isoFormat('dddd'));
        $days         = $this->getMatchingDays($convertedDay);

        return $schedule->schedule_availabilities()->whereIn('days', $days)->get();
    }

    protected function availabilitiesToCarbonPeriod($date, $availabilities)
    {
        $periods = [];
        foreach ($availabilities as $availability) {
            $periods[] = new \Carbon\CarbonPeriod("{$date} {$availability->start_time}", self::STEP, "{$date} {$availability->end_time}");
        }

        return $periods;
    }

    protected function excludeTimes($periods, $excludedHours)
    {
        foreach ($periods as $period) {
            foreach ($excludedHours as $excludedHour) {
                $period->filter(function ($date) use ($excludedHour) {
                    return !$date->between($excludedHour['from'], $excludedHour['to']);
                });
            }
        }
    }

    protected function getExcludedTimes(Schedule $schedule, $date, $buffer)
    {
        $bookings = $schedule->bookings()
            ->where('datetime_from', '>=', "{$date} 00:00:00")
            ->where('datetime_from', '<=', "{$date} 23:59:59")
            ->get();

        $excludedTimes = [];
        foreach ($bookings as $booking) {
            $excludedTimes[] = [
                'from' => Carbon::parse($booking->datetime_from)->subMinutes($buffer)->addSecond(),
                'to'   => Carbon::parse($booking->datetime_to)->addMinutes($buffer)->subSecond()
            ];
        }

        return $excludedTimes;
    }

    protected function toTimes(array $periods): array
    {
        $flatTimes = [];
        foreach ($periods as $period) {
            foreach ($period as $time) {
                /** @var Carbon $time */
                $flatTimes[] = $time->format('H:i');
            }
        }

        return $flatTimes;
    }
}
