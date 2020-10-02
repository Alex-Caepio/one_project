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
            'timezone_id' => $this->faker->randomDigit,
        ];
    }
}
