<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Keyword;
use Faker\Generator as Faker;

$factory->define(Keyword::class, function (Faker $faker) {
    return [
        'title' => $faker->word(),
    ];
});
