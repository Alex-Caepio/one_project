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
            'cost'              => 500,
            'schedule_id'       => $this->faker->randomNumber(3),
            'name'              => config('app.platform_currency'),
            'is_free'           => $this->faker->boolean,
            'available_till'    => $this->faker->date(),
            'min_purchase'      => $this->faker->randomDigit,
            'number_available'  => $this->faker->randomDigit,
            'duration'          => 60
        ];
    }
}
