<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Schedule;
use Faker\Generator as Faker;

$factory->define(Schedule::class, function (Faker $faker) {
    return [
        'title'              => $faker->sentence(),
        'service_id'         => $faker->randomNumber(3),
        'location_id'        => $faker->randomNumber(3),
        'start_date'         => $faker->date(),
        'end_date'           => $faker->date(),
        'attendees'          => $faker->randomNumber(2),
        'comments'           => $faker->realText(),
        'venue'              => $faker->city,
        'city'               => $faker->city,
        'country'            => $faker->country,
    ];
});
