<?php

namespace Database\Factories;


use App\Models\ScheduleFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFileFactory extends Factory
{

    protected $model = ScheduleFile::class;

    public function definition()
    {
        return [
            'schedule_id'   => $this->faker->randomNumber(2),
            'url'           => $this->faker->url,
        ];
    }
}

