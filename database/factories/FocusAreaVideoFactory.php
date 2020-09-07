<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FocusAreaVideo;
use Faker\Generator as Faker;

$factory->define(FocusAreaVideo::class, function (Faker $faker) {
    return [
        'focus_area_id'         => $faker->randomNumber(3),
        'link'                         => $faker->url,
    ];
});
