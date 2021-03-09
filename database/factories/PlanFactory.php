<?php

namespace Database\Factories;


use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{

    protected $model = Plan::class;

    public function definition()
    {
        return [
            'name'                            => $this->faker->sentence(2),
            'list_paid_services'              => $this->faker->boolean,
            'list_free_services'              => $this->faker->boolean,
            'unlimited_bookings'              => $this->faker->boolean,
            'schedules_per_service_unlimited' => $this->faker->boolean,
        ];
    }

    public function best()
    {
        return $this->state(function (array $attributes) {
            return [
                'list_paid_services'              => true,
                'list_free_services'              => true,
                'unlimited_bookings'              => true,
                'schedules_per_service_unlimited' => true,
            ];
        });
    }
}
