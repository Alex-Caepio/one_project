<?php

namespace Database\Factories;


use App\Models\ScheduleFreeze;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFreezeFactory extends Factory
{

    protected $model = ScheduleFreeze::class;

    public function definition()
    {
        return [
            'schedule_id'         => $this->faker->randomNumber(2),
            'user_id'             => $this->faker->randomNumber(2),
            'freeze_at'           => Carbon::now(),
        ];
    }
}
