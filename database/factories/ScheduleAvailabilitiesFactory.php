<?php

namespace Database\Factories;


use App\Models\ScheduleAvailabilities;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleAvailabilitiesFactory extends Factory
{

    protected $model = ScheduleAvailabilities::class;

    public function definition()
    {
        return [
            'schedule_id'   => $this->faker->randomNumber(2),
            'days'          => $this->faker->randomElement(['everyday', 'weekdays', 'weekends', 'monday',
                'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
            'start_time'    => $this->faker->time(),
            'end_time'      => $this->faker->time(),
        ];
    }
}

