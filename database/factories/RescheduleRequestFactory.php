<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\RescheduleRequest;
use Faker\Generator as Faker;

$factory->define(RescheduleRequest::class, function (Faker $faker) {
    return [
        'schedule_id' => $faker->randomNumber(5),
        'user_id' => $faker->randomNumber(5),
        'new_schedule_id' => $faker->randomNumber(5)
    ];
});
