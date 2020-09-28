<?php

namespace Database\Factories;


use App\Models\RescheduleRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class RescheduleRequestFactory extends Factory
{

    protected $model = RescheduleRequest::class;

    public function definition()
    {
        return [
            'schedule_id' => $this->faker->randomNumber(5),
            'user_id' => $this->faker->randomNumber(5),
            'new_schedule_id' => $this->faker->randomNumber(5)
        ];
    }
}
