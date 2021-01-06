<?php

namespace Database\Factories;

use App\Models\Timezone;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimezoneFactory extends Factory
{
    protected $model = Timezone::class;

    public function definition()
    {
        return [
            'value'        => $this->faker->sentence(),
            'label'        => $this->faker->sentence(),
        ];
    }
}
