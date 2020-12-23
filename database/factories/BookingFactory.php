<?php

namespace Database\Factories;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{

    protected $model = Booking::class;

    public function definition()
    {
        return [
            'user_id'           => $this->faker->randomNumber(3),
            'schedule_id'       => $this->faker->randomNumber(2),
            'price_id'          => $this->faker->randomNumber(1),
            'availability_id'   => $this->faker->randomNumber(6),
            'purchase_id'       => $this->faker->randomNumber(6),
            'datetime_from'     => Carbon::tomorrow()->format('Y-m-d H:i:s'),
            'datetime_to'       => Carbon::tomorrow()->addHour()->format('Y-m-d H:i:s'),
            'cost'              => 100,
            'quantity'          => $this->faker->randomNumber(),
            'reference'          => $this->faker->text(6),
            'created_at'        => $this->faker->date("Y-m-d H:i:s"),
            'updated_at'        => $this->faker->date("Y-m-d H:i:s"),
        ];
    }
}

