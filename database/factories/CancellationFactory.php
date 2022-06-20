<?php

namespace Database\Factories;

use App\Models\Cancellation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CancellationFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Cancellation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        $feeFlag = $this->faker->boolean(50);
        return [
            'user_id'             => $this->faker->randomNumber(3),
            'purchase_id'         => $this->faker->randomNumber(2),
            'practitioner_id'     => $this->faker->randomNumber(1),
            'booking_id'          => $this->faker->randomNumber(6),
            'fee'                 => $feeFlag ? $this->faker->randomNumber() : null,
            'amount'              => $this->faker->randomNumber(),
            'stripe_id'           => $feeFlag ? $this->faker->word() : null,
            'cancelled_by_client' => $this->faker->boolean(50)
        ];
    }
}
