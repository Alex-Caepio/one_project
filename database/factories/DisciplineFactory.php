<?php


namespace Database\Factories;


use App\Models\Discipline;
use Illuminate\Database\Eloquent\Factories\Factory;


class DisciplineFactory extends Factory
{

    protected $model = Discipline::class;

    public function definition()
    {
        $name = $this->faker->text(20);
        return [
            'name'         => $name,
            'url'          => to_url($name),
            'icon_url'          => $this->faker->imageUrl(),
            'is_published' => $this->faker->boolean,
        ];
    }
}
