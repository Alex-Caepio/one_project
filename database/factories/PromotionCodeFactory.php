<?php

namespace Database\Factories;


use App\Models\PromotionCode;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionCodeFactory extends Factory
{

    protected $model = PromotionCode::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(),
            'uses_per_client' => $this->faker->randomNumber(5),
            'uses_per_code' => $this->faker->randomNumber(5),
            'promotion_id' => $this->faker->randomNumber(5),
        ];
    }
}
