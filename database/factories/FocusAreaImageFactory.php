<?php

namespace Database\Factories;

use App\Models\FocusAreaImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FocusAreaImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FocusAreaImage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'focus_area_id' => $this->faker->randomNumber(3),
            'path' => $this->faker->url,
        ];
    }
}
