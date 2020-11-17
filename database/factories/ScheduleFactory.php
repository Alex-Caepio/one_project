<?php

namespace Database\Factories;


use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{

    protected $model = Schedule::class;

    public function definition()
    {
        return [
            'title'              => $this->faker->sentence(),
            'service_id'         => $this->faker->randomNumber(2),
            'location_id'        => $this->faker->randomNumber(2),
            'start_date'         => '2020-9-5',
            'end_date'           => $this->faker->date(),
            'attendees'          => $this->faker->randomNumber(2),
            'comments'           => $this->faker->realText(),
            'venue'              => $this->faker->city,
            'city'               => $this->faker->city,
            'country'            => $this->faker->country,
            'url'                => $this->faker->url,
        ];
    }
}
