<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ServiceType;
use Faker\Generator as Faker;

$factory->define(ServiceType::class, function (Faker $faker) {
    return [
        'name'                       => $faker->sentence(),
    ];
});
