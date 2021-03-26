<?php

namespace App\Actions\Schedule;


use App\Models\Price;
use App\Models\Schedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class GetAvailableAppointmentTimeOnDate
{
    public const STEP = '15 minutes';

    public function execute(Price $price, $date)
    {
        $schedule = $price->schedule;
        $availabilities = $this->getAvailabilitiesMatchingDate($date, $schedule);
        $periods        = $this->availabilitiesToCarbonPeriod($date, $availabilities);
        $excludedTimes  = $this->getExcludedTimes($schedule, $date, $schedule->buffer_time, $price->duration ?? 0);

        $this->excludeTimes($periods, $excludedTimes);

        $flatTimes = $this->toTimes($periods);
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
            $from = Carbon::parse("{$date} {$availability->start_time}");
            $to   = Carbon::parse("{$date} {$availability->end_time}");

            if ($from->greaterThanOrEqualTo($to)) {
                $to->addDay();
            }

            $periods[] = new \Carbon\CarbonPeriod($from, self::STEP, $to);
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

    protected function getExcludedTimes(Schedule $schedule, $date, $buffer, $duration = 0)
    {
        $bookings = $schedule->bookings()
            ->where('datetime_from', '>=', "{$date} 00:00:00")
            ->where('datetime_from', '<=', "{$date} 23:59:59")
            ->get();

        $unavailabilities = $schedule->schedule_unavailabilities()
            ->where('start_date', '>=', "{$date} 00:00:00")
            ->where('end_date', '<=', "{$date} 23:59:59")
            ->get();

        $frozenBookings = $schedule->freezes()
            ->where('freeze_at', '>', Carbon::now()->subMinutes(15)->toDateTimeString())
            ->get();

        $excludedTimes = [];
        foreach ($bookings as $booking) {
            $excludedTimes[] = [
                'from' => Carbon::parse($booking->datetime_from)->subMinutes($buffer + $duration)->addSecond(),
                'to'   => Carbon::parse($booking->datetime_to)->addMinutes($buffer)->subSecond()
            ];
        }

        foreach ($unavailabilities as $unavailability) {
            $excludedTimes[] = [
                'from' => Carbon::parse($unavailability->start_date)->subMinutes($duration)->addSecond(),
                'to'   => Carbon::parse($unavailability->end_date)->subSecond()
            ];
        }

        foreach ($frozenBookings as $frozenBooking) {
            $excludedTimes[] = [
                'from' => Carbon::parse($frozenBooking->freeze_at)->subMinutes($duration)->addSecond(),
                'to'   => Carbon::parse($frozenBooking->freeze_at)->addMinutes(15)->subSecond()
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
