<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PromotionCode;
use Faker\Generator as Faker;

$discount_type = [
    'percentage','fixed'
];
$factory->define(PromotionCode::class, function (Faker $faker) use ($discount_type){
    return [
        'name'              => $faker->sentence(),
        'valid_from'         => $faker->date(),
        'expiry_date'         => $faker->date(),
        'discount_type' => $faker->randomElement($discount_type),
        'discount_value' => $faker->randomNumber(5),
        'service_type_id' => $faker->randomNumber(5),
        'discipline_id' => $faker->randomNumber(5),
        'focus_area_id' => $faker->randomNumber(5),
        'max_uses_per_code' => $faker->randomNumber(5),
        'code_uses_per_customer' => $faker->randomNumber(5),
    ];
});
