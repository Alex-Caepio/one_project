<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Price;
use Faker\Generator as Faker;

$factory->define(Price::class, function (Faker $faker) {
    return [
        'amount' => $faker->randomDigitNotNull,
        'schedule_id' => $faker->randomNumber(3),
    ];
});
