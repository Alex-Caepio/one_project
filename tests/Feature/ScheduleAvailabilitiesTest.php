<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\ScheduleAvailability;
use App\Models\ScheduleUnavailability;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ScheduleAvailabilitiesTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_finds_one_outstanding_booking_on_monday()
    {
        $schedule = Schedule::factory()->create();
        ScheduleAvailability::factory()->create([
            'schedule_id' => $schedule->id,
            'days'        => 'monday',
            'start_time'  => '09:00:00',
            'end_time'    => '19:00:00',
        ]);
        Booking::factory()->create(['schedule_id' => $schedule->id, 'datetime_from' => '2020-12-21 08:00:00']);
        Booking::factory()->create(['schedule_id' => $schedule->id, 'datetime_from' => '2020-12-21 10:00:00']);

        $bookings = $schedule->getOutsiderBookings();
        $this->assertCount(1, $bookings);
    }

    public function test_finds_one_outstanding_booking_because_of_unavailability()
    {
        $schedule = Schedule::factory()->create();
        ScheduleUnavailability::factory()->create([
            'schedule_id' => $schedule->id,
            'start_date'  => '2020-12-21 09:00:00',
            'end_date'    => '2020-12-21 19:00:00',
        ]);
        Booking::factory()->create(['schedule_id' => $schedule->id, 'datetime_from' => '2020-12-21 08:00:00']);
        Booking::factory()->create(['schedule_id' => $schedule->id, 'datetime_from' => '2020-12-21 10:00:00']);

        $bookings = $schedule->getOutsiderBookings();
        $this->assertCount(1, $bookings);
    }
}
