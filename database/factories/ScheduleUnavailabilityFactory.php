<?php

namespace Database\Factories;


use App\Models\ScheduleUnavailability;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleUnavailabilityFactory extends Factory
{

    protected $model = ScheduleUnavailability::class;

    public function definition()
    {
        return [
            'schedule_id'   => $this->faker->randomNumber(2),
            'start_date'    => '2020-9-5',
            'end_date'      => $this->faker->date(),
        ];
    }
}

