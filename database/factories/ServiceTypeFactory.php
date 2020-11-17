<?php

namespace Database\Factories;


use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceTypeFactory extends Factory
{

    protected $model = ServiceType::class;

    public function definition()
    {
        return [
            'id'   => 'econtent',
            'name' => $this->faker->sentence(),
        ];
    }
}
