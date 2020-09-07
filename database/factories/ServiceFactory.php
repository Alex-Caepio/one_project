<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/


$factory->define(\App\Models\Service::class, function (Faker $faker) {
    return [
        'title'                       => $faker->sentence(),
        'keyword_id'                     => $faker->randomDigit,
        'user_id'                     => $faker->randomDigit,
        'description'                 => $faker->text(255),
        'is_published'                => $faker->boolean,
        'introduction'                => $faker->text,
        'url'                         => $faker->url,
    ];
});
