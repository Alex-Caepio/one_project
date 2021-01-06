<?php

namespace Database\Factories;


use App\Models\FocusArea;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FocusAreaFactory extends Factory {

    protected $model = FocusArea::class;

    public function definition() {
    return [
            'name'         => $this->faker->word(2),
            'description'  => $this->faker->sentence(5),
            'introduction' => $this->faker->sentence(1),
            'url'          => $this->faker->url,
            'icon_url'     => $this->faker->imageUrl(),
            'banner_url'   => $this->faker->imageUrl(),
        ];
    }
}
