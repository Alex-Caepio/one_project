<?php


namespace Database\Factories;


use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition()
    {
        return [
            'iso' => Str::random(2),
            'name' => Str::random(10),
            'nicename' => Str::random(10),
            'iso3' => Str::random(3),
            'numcode' => $this->faker->randomDigit,
            'phonecode' => $this->faker->randomDigit,
        ];
    }
}
