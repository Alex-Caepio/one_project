<?php


namespace Database\Factories;


use App\Models\DisciplineVideo;
use Illuminate\Database\Eloquent\Factories\Factory;

class DisciplineVideoFactory extends Factory
{

    protected $model = DisciplineVideo::class;

    public function definition()
    {
        return [
            'discipline_id' => $this->faker->randomNumber(3),
            'link' => $this->faker->url,
        ];
    }
}
