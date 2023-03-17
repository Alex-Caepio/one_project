<?php

namespace Tests\Unit;

use App\Models\ScheduleAvailability;
use PHPUnit\Framework\TestCase;

class ScheduleAvailabilityTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_fitsDay_method()
    {
        $availability       = new ScheduleAvailability();
        $availability->days = 'weekends';

        //Sunday is a weekend
        $this->assertTrue($availability->fitsDay('2020-11-29'));

        //Monday is not a weekend
        $this->assertFalse($availability->fitsDay('2020-11-30'));
    }

    public function test_fitsDay_method_monday()
    {
        $availability       = new ScheduleAvailability();
        $availability->days = 'Monday';

        //Monday is the correct day
        $this->assertTrue($availability->fitsDay('2020-11-30'));

        //Tuesday is not correct day
        $this->assertFalse($availability->fitsDay('2020-12-01'));
    }

    public function test_fitsDay_method_tuesday()
    {
        $availability       = new ScheduleAvailability();
        $availability->days = 'Tuesday';

        //Tuesday is the correct day
        $this->assertTrue($availability->fitsDay('2020-12-01'));

        //Wednesday is not correct day
        $this->assertFalse($availability->fitsDay('2020-12-02'));
    }

    public function test_fitsDay_method_wednesday()
    {
        $availability       = new ScheduleAvailability();
        $availability->days = 'Wednesday';

        //Wednesday is the correct day
        $this->assertTrue($availability->fitsDay('2020-12-02'));

        //Thursday is not correct day
        $this->assertFalse($availability->fitsDay('2020-12-03'));
    }

    public function test_fitsDay_method_thursday()
    {
        $availability       = new ScheduleAvailability();
        $availability->days = 'Thursday';

        //Thursday is the correct day
        $this->assertTrue($availability->fitsDay('2020-12-03'));

        //Friday is not correct day
        $this->assertFalse($availability->fitsDay('2020-12-04'));
    }

    public function test_fitsDay_method_friday()
    {
        $availability       = new ScheduleAvailability();
        $availability->days = 'Friday';

        //Friday is the correct day
        $this->assertTrue($availability->fitsDay('2020-12-04'));

        //Saturday is not correct day
        $this->assertFalse($availability->fitsDay('2020-12-05'));
    }

    public function test_fitsDay_method_saturday()
    {
        $availability       = new ScheduleAvailability();
        $availability->days = 'Saturday';

        //Saturday is the correct day
        $this->assertTrue($availability->fitsDay('2020-12-05'));

        //Sunday is not correct day
        $this->assertFalse($availability->fitsDay('2020-12-06'));
    }

    public function test_fitsDay_method_everyday()
    {
        $availability       = new ScheduleAvailability();
        $availability->days = 'everyday';

        //This is a correct day
        $this->assertTrue($availability->fitsDay('2020-12-01'));
    }

    public function test_fitsDay_method_weekdays()
    {
        $availability       = new ScheduleAvailability();
        $availability->days = 'weekdays';

        //Friday is a weekday
        $this->assertTrue($availability->fitsDay('2020-12-04'));

        //Sunday is a weekend
        $this->assertFalse($availability->fitsDay('2020-12-05'));
    }

    public function test_fitsTime_method()
    {

        //Case 1. Regular time
        $availability = new ScheduleAvailability([
            'start_time' => '10:00:00',
            'end_time'   => '18:00:00'
        ]);
        $this->assertTrue($availability->fitsTime('10:00:00'));
        $this->assertTrue($availability->fitsTime('13:00:00'));
        $this->assertTrue($availability->fitsTime('18:00:00'));
        $this->assertFalse($availability->fitsTime('09:00:00'));
        $this->assertFalse($availability->fitsTime('18:00:01'));
        $this->assertFalse($availability->fitsTime('19:00:00'));


        //Case 2. Inverted time (21:00 - 03:00)
        $availability = new ScheduleAvailability([
            'start_time' => '21:00:00',
            'end_time'   => '03:00:00'
        ]);
        $this->assertTrue($availability->fitsTime('21:00:00'));
        $this->assertFalse($availability->fitsTime('04:00:00'));
        $this->assertFalse($availability->fitsTime('20:00:01'));


        //Case 3. Looped time (12:00 - 12:00)
        $availability = new ScheduleAvailability([
            'start_time' => '21:00:00',
            'end_time'   => '21:00:00'
        ]);
        $this->assertTrue($availability->fitsTime('21:00:00'));
        $this->assertTrue($availability->fitsTime('22:00:00'));
        $this->assertTrue($availability->fitsTime('20:00:01'));
    }
}
