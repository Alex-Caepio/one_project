<?php

namespace Database\Factories;


use App\Models\FocusArea;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FocusAreaFactory extends Factory
{

    protected $model = FocusArea::class;

    public function definition()
    {
        return [
            'name'         => $this->faker->sentence(),
            'description'  => Str::random(10),
            'introduction' => Str::random(10),
            'url'          => $this->faker->url,
            'icon_url'     => $this->faker->imageUrl(),
            'banner_url'   => $this->faker->imageUrl(),
        ];
    }
}
