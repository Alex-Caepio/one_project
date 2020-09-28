<?php

namespace Database\Factories;


use App\Models\Price;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceFactory extends Factory
{

    protected $model = Price::class;

    public function definition()
    {
        return [
            'amount' => $this->faker->randomDigitNotNull,
            'schedule_id' => $this->faker->randomNumber(3),
        ];
    }
}
