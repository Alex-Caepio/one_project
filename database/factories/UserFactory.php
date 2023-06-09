<?php

namespace Database\Factories;


use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{

    protected $model = User::class;

    public function definition()
    {
        $type = ['client', 'practitioner'];

        return [
            'first_name' => Str::random(10),
            'last_name' => Str::random(10),
            'account_type' => $this->faker->randomElement($type),
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '12dsfsdDDD', // password
            'remember_token' => Str::random(10),
            'business_name' => $this->faker->sentence(),
            'business_address' => $this->faker->sentence(),
            'business_email' => $this->faker->sentence(),
            'business_introduction' => $this->faker->sentence(),
            'business_time_zone_id' => $this->faker->randomDigit,
            'business_country_id' => $this->faker->randomNumber(),
            'country_id' => $this->faker->randomNumber(),
            'business_city' => $this->faker->sentence(),
            'business_postal_code' => $this->faker->sentence(),
            'business_time_zone' => $this->faker->sentence(),
            'business_vat' => $this->faker->sentence(),
            'business_company_houses_id' => $this->faker->sentence()
        ];
    }
}
