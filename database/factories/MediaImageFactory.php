<?php

namespace Database\Factories;


use App\Models\MediaImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaImageFactory extends Factory
{

    protected $model = MediaImage::class;

    public function definition()
    {
        return [
            'model_id'     => $this->faker->randomDigit,
            'model_name'   => $this->faker->text(255),
            'url'          => $this->faker->url,
        ];
    }
}
