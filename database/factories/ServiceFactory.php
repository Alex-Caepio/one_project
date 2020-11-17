<?php

namespace Database\Factories;


use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{

    protected $model = Service::class;

    public function definition()
    {
        return [
            'title'                       => $this->faker->sentence(),
            'keyword_id'                  => $this->faker->randomDigit,
            'user_id'                     => $this->faker->randomDigit,
            'description'                 => $this->faker->text(255),
            'is_published'                => $this->faker->boolean,
            'introduction'                => $this->faker->text,
            'url'                         => $this->faker->url,
            'service_type_id'             => $this->faker->randomNumber(2),
        ];
    }
}
