<?php

namespace Database\Factories;


use App\Models\ScheduleHiddenFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleHiddenFileFactory extends Factory
{

    protected $model = ScheduleHiddenFile::class;

    public function definition()
    {
        return [
            'schedule_id'   => $this->faker->randomNumber(2),
            'url'           => $this->faker->url,
        ];
    }
}

