<?php

namespace Database\Factories;


use App\Models\MediaVideo;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaVideoFactory extends Factory
{

    protected $model = MediaVideo::class;

    public function definition()
    {
        return [
            'model_id'     => $this->faker->randomDigit,
            'model_name'   => $this->faker->text(255),
            'url'          => $this->faker->url,
        ];
    }
}
