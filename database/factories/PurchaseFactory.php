<?php


namespace Database\Factories;


use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PurchaseFactory extends Factory {
    protected $model = Purchase::class;

    public function definition() {
        return [
            'user_id'        => $this->faker->randomNumber(3),
            'schedule_id'    => $this->faker->randomNumber(2),
            'service_id'     => $this->faker->randomNumber(2),
            'promocode_id'   => null,
            'price_id'       => null,
            'reference'      => Str::random(6),
            'price'          => $this->faker->randomFloat(2, 5, 400),
            'price_original' => $this->faker->randomFloat(2, 5, 400),
            'created_at'     => $this->faker->date("Y-m-d H:i:s"),
            'updated_at'     => $this->faker->date("Y-m-d H:i:s"),
            'is_deposit'     => false,
            'deposit_amount' => $this->faker->randomFloat(2, 5, 400),
        ];
    }
}
