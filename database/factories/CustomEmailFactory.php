<?php

namespace Database\Factories;

use App\Models\CustomEmail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomEmailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomEmail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user_type = [
            'client', 'practitioner'
        ];
        return [
            'name' => $this->faker->sentence(),
            'user_type' => $this->faker->randomElement($user_type),
            'from_email' => $this->faker->unique()->safeEmail,
            'from_title' => $this->faker->sentence(),
            'subject' => $this->faker->realText(),
            'logo' => $this->faker->sentence(),
            'text' => $this->faker->text(),
            'delay'         => $this->faker->randomNumber(3)
        ];
    }
}
