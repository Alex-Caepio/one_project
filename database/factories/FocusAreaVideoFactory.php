<?php

namespace Database\Factories;


use App\Models\FocusAreaVideo;
use Illuminate\Database\Eloquent\Factories\Factory;

class FocusAreaVideoFactory extends Factory
{

    protected $model = FocusAreaVideo::class;

    public function definition()
    {
        return [
            'focus_area_id' => $this->faker->randomNumber(3),
            'link' => $this->faker->url,
        ];
    }
}
