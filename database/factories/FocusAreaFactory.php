<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FocusArea;
use Faker\Generator as Faker;

$factory->define(FocusArea::class, function (Faker $faker) {
    return [
        'name'              => $faker->sentence(),
        'url'                         => $faker->url,
    ];
});
