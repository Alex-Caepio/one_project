<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DisciplineVideo;
use Faker\Generator as Faker;

$factory->define(DisciplineVideo::class, function (Faker $faker) {
    return [
    'discipline_id'         => $faker->randomNumber(3),
     'link'                         => $faker->url,
    ];
});
