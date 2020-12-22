<?php


namespace Database\Factories;


use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition()
    {
        return [
            'user_id'           => $this->faker->randomNumber(3),
            'schedule_id'       => $this->faker->randomNumber(2),
            'price_id'          => $this->faker->randomNumber(1),
            'promocode'         => $this->faker->randomAscii,
            'price'             => $this->faker->randomFloat(),
            'price_original'    => $this->faker->randomFloat(),
            'created_at'        => $this->faker->date("Y-m-d H:i:s"),
            'updated_at'        => $this->faker->date("Y-m-d H:i:s"),
            'is_deposit'        => $this->faker->boolean,
            'deposit_amount'    => $this->faker->randomFloat(),
        ];
    }
}
