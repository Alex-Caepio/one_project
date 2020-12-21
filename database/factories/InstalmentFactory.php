<?php


namespace Database\Factories;

use App\Models\Instalment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstalmentFactory extends Factory
{
    protected $model = Instalment::class;

    public function definition()
    {
        return [
            'user_id'           => $this->faker->randomNumber(3),
            'schedule_id'       => $this->faker->randomNumber(2),
            'price_id'          => $this->faker->randomNumber(1),
            'purchase_id'       => $this->faker->randomNumber(6),
            'payment_date'      => $this->faker->date("Y-m-d H:i:s"),
            'is_paid'           => $this->faker->boolean,
            'payment_amount'    => $this->faker->randomFloat(),
            'created_at'        => $this->faker->date("Y-m-d H:i:s"),
            'updated_at'        => $this->faker->date("Y-m-d H:i:s"),
            'deleted_at'        => $this->faker->date("Y-m-d H:i:s"),
        ];
    }
}
