<?php

namespace Database\Factories;


use App\Models\MediaFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFileFactory extends Factory
{

    protected $model = MediaFile::class;

    public function definition()
    {
        return [
            'model_id'     => $this->faker->randomDigit,
            'model_name'   => $this->faker->text(255),
            'url'          => $this->faker->url,
        ];
    }
}
