<?php

namespace Database\Factories;


use App\Models\Keyword;
use Illuminate\Database\Eloquent\Factories\Factory;

class KeywordFactory extends Factory
{

    protected $model = Keyword::class;

    public function definition()
    {
        return [
            'title' => $this->faker->word(),
        ];
    }
}
