<?php

namespace Database\Factories;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NotificationFactory extends Factory {

    protected $model = Notification::class;

    public function definition() {
        return [
            'title' => Str::random(6),
            'client_id' => $this->faker->randomNumber(3),
            'practitioner_id' => $this->faker->randomNumber(4),
            'receiver_id' => $this->faker->randomNumber(3),
            'old_address'  => $this->faker->address,
            'new_address' => $this->faker->address,
            'price_id' => $this->faker->randomNumber(3),
            'price_payed' => $this->faker->randomNumber(),
            'price_refunded' => $this->faker->randomNumber(),
            'type' => $this->faker->randomElement([
                'reschedule_declined_by_client',
                'reschedule_declined_by_practitioner',
                'reschedule_accepted_by_client',
                'reschedule_accepted_by_practitioner',
                'booking_canceled_by_client',
                'booking_canceled_by_practitioner',]),

            'old_datetime'  => Carbon::tomorrow()->format('Y-m-d H:i:s'),
            'new_datetime'  => Carbon::tomorrow()->addHour()->format('Y-m-d H:i:s'),
            'read_at'       => Carbon::tomorrow()->format('Y-m-d H:i:s'),
            'created_at'    => Carbon::tomorrow()->format('Y-m-d H:i:s'),
            'updated_at'    => Carbon::tomorrow()->format('Y-m-d H:i:s'),
            'datetime_from' => Carbon::tomorrow()->format('Y-m-d H:i:s'),
            'datetime_to'   => Carbon::tomorrow()->addHour()->format('Y-m-d H:i:s'),
        ];
    }
}

