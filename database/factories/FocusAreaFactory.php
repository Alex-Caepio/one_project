<?php

namespace Database\Factories;


use App\Models\FocusArea;
use App\Models\FocusAreaImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class FocusAreaFactory extends Factory
{

    protected $model = FocusAreaImage::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(),
            'url' => $this->faker->url,
        ];
    }
}
