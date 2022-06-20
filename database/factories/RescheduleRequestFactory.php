<?php

namespace Database\Factories;


use App\Models\RescheduleRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class RescheduleRequestFactory extends Factory {

    protected $model = RescheduleRequest::class;

    public function definition() {
        return [
            'schedule_id'            => $this->faker->randomNumber(5),
            'user_id'                => $this->faker->randomNumber(5),
            'new_schedule_id'        => $this->faker->randomNumber(5),
            'booking_id'             => $this->faker->randomNumber(5),
            'new_price_id'           => $this->faker->randomNumber(5),
            'comment'                => $this->faker->realText(150),
            'old_location_displayed' => $this->faker->address,
            'new_location_displayed' => $this->faker->address,
            'old_start_date'         => Carbon::now()->format('Y-m-d H:i:s'),
            'new_start_date'         => Carbon::tomorrow()->addHour()->format('Y-m-d H:i:s'),
            'old_end_date'           => Carbon::now()->format('Y-m-d H:i:s'),
            'new_end_date'           => Carbon::tomorrow()->addHour()->format('Y-m-d H:i:s'),
            'requested_by'           => $this->faker->randomElement(['client', 'practitioner'])
        ];
    }
}
