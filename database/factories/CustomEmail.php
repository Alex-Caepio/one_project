<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CustomEmail;
use Faker\Generator as Faker;

$user_type = [
    'client', 'practitioner'
];

$factory->define(CustomEmail::class, function (Faker $faker) use ($user_type) {
    return [
        'name' => $faker->sentence(),
        'user_type' => $faker->randomElement($user_type),
        'from_email' => $faker->unique()->safeEmail,
        'from_title' => $faker->sentence(),
        'subject' => $faker->realText(),
        'logo' => $faker->sentence(),
        'text' => $faker->text(),
        'delay'         => $faker->randomNumber(3)
    ];
});
