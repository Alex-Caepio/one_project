<?php

namespace App\Actions\Schedule;

use App\Models\Booking;
use App\Models\Price;
use App\Models\Schedule;
use App\Models\ScheduleAvailability;
use App\Models\UserUnavailabilities;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection;

class GetAvailableAppointmentTimeOnDate
{
    public const STEP = '15 minutes';
    public const STEP_VALUE = '15';

    public function execute(Price $price, string $date, string $timezone): array
    {
        $schedule = $price->schedule;

        /** @var Collection|ScheduleAvailability[] $availabilities */
        $availabilities = $this->getAvailabilitiesMatchingDate($date, $schedule);
        $periods = $this->availabilitiesToCarbonPeriod($date, $price, $availabilities);
        $excludedTimes = $this->getExcludedTimes(
            $schedule,
            $date,
            $this->getTodayBuffer($schedule),
            $price->duration ?? 0
        );

        $this->excludeTimes($periods, $excludedTimes);
        $flatTimes = $this->toTimes($periods, $timezone);

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
        $days = $this->getMatchingDays($convertedDay);

        return $schedule->schedule_availabilities()->whereIn('days', $days)->get();
    }

    /**
     * @param string $date
     * @param Collection|ScheduleAvailability[] $availabilities
     *
     * @return CarbonPeriod[]
     */
    protected function availabilitiesToCarbonPeriod(string $date, Price $price, Collection $availabilities): array
    {
        $periods = [];
        foreach ($availabilities as $availability) {
            if ($date === Carbon::now()->format('Y-m-d')) {
                $startTime = Carbon::now()->format('H:i:s');
            } else {
                $startTime = $availability->start_time;
            }

            $from = $this->roundMinutes(Carbon::parse("{$date} {$startTime}"));
            $to = $this->roundMinutes(Carbon::parse("{$date} {$availability->end_time}")->subMinutes($price->duration));

            if ($from->greaterThanOrEqualTo($to) && $date !== Carbon::now()->format('Y-m-d')) {
                $to->addDay();
            }

            $periods[] = new CarbonPeriod($from, self::STEP, $to);
        }

        return $periods;
    }

    protected function excludeTimes($periods, array $excludedHours)
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
        $scheduleIds = Schedule::where('service_id', $schedule->service_id)->pluck('id');

        $excludedTimes = [];

        // Exclude today past + schedule buffer between appointments
        if (Carbon::parse($date)->isToday()) {
            $excludedTimes[] = [
                'from' => Carbon::now()->startOfDay()->addSecond(),
                'to' => Carbon::now()->addMinutes($buffer)->subSecond()
            ];
        }

        $bookings = Booking::whereIn('schedule_id', $scheduleIds)
            ->where('datetime_from', '>=', "{$date} 00:00:00")
            ->where('datetime_from', '<=', "{$date} 23:59:59")
            ->where('status', '!=', 'canceled')
            ->get();

        $unavailabilities = $schedule->schedule_unavailabilities()
            ->where('start_date', '>=', "{$date} 00:00:00")
            ->where('end_date', '<=', "{$date} 23:59:59")
            ->get();

        $frozenBookings = $schedule->freezes()
            ->where('freeze_at', '>', Carbon::now()->subMinutes(15)->toDateTimeString())
            ->get();

        foreach ($bookings as $booking) {
            $excludedTimes[] = [
                'from' => Carbon::parse($booking->datetime_from)->subMinutes($schedule->buffer_time + $duration)->addSecond(),
                'to' => Carbon::parse($booking->datetime_to)->addMinutes($schedule->buffer_time)->subSecond()
            ];
        }

        foreach ($unavailabilities as $unavailability) {
            $excludedTimes[] = [
                'from' => Carbon::parse($unavailability->start_date)->subMinutes($duration)->addSecond(),
                'to' => Carbon::parse($unavailability->end_date)->subSecond()
            ];
        }

        $globalUnavailabilities = UserUnavailabilities::where('practitioner_id', $schedule->service->user_id)->get();
        foreach ($globalUnavailabilities as $unavailability) {
            $excludedTimes[] = [
                'from' => Carbon::parse($unavailability->start_date)->subMinutes($duration)->addSecond(),
                'to' => Carbon::parse($unavailability->end_date)->subSecond()
            ];
        }

        foreach ($frozenBookings as $frozenBooking) {
            $excludedTimes[] = [
                'from' => Carbon::parse($frozenBooking->freeze_at)->subMinutes($duration)->addSecond(),
                'to' => Carbon::parse($frozenBooking->freeze_at)->addMinutes(15)->subSecond()
            ];
        }


        return $excludedTimes;
    }

    protected function toTimes(array $periods, string $timezone): array
    {
        $flatTimes = [];
        foreach ($periods as $period) {
            foreach ($period as $time) {
                /** @var Carbon $time */
                $time->setTimezone($timezone);
                $flatTimes[] = $time->format('H:i');
            }
        }

        return $flatTimes;
    }


    protected function roundMinutes(Carbon $date)
    {
        $s = self::STEP_VALUE * 60;
        $date->setTimestamp($s * ceil($date->getTimestamp() / $s));

        return $date;
    }

    /**
     * Returns delay between now and first available appointment booking in minutes
     *
     * @param Schedule $schedule
     * @return int
     */
    private function getTodayBuffer(Schedule $schedule): int
    {
        switch ($schedule->notice_min_period) {
            case "hours":
                $multiplier = 60;
                break;
            case "days":
                $multiplier = 60 * 24;
                break;
            default:
                $multiplier = 1;
                break;
        }

        return $schedule->notice_min_time * $multiplier;
    }
}
