<?php

namespace Database\Factories;


use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{

    protected $model = Article::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(5),
            'description' => $this->faker->text(255),
            'is_published' => $this->faker->boolean,
            'introduction' => $this->faker->text,
            'url' => $this->faker->url,
        ];
    }
}

