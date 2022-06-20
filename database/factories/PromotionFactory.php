<?php


namespace Database\Factories;


use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    public function definition()
    {

        return [
            'name' => $this->faker->sentence,
            'valid_from' => '2020-2-3',
            'expiry_date' => '2020-12-3',
//            'discipline_id' => $this->faker->randomNumber(2),
//            'service_type_id' => $this->faker->randomNumber(2),
//            'focus_area_id' => $this->faker->randomNumber(2),
        ];
    }
}
