<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Article;
use Faker\Generator as Faker;

$factory->define(Article::class, function (Faker $faker) {
    return [
        'title'        => $faker->sentence(5),
        'description'  => $faker->text(255),
        'user_id'      => 1,
        'is_published' => $faker->boolean,
        'introduction' => $faker->text,
        'url'          => $faker->url,
    ];
});
