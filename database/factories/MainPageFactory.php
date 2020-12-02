<?php

namespace Database\Factories;


use App\Models\MainPage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MainPageFactory extends Factory
{

    protected $model = MainPage::class;

    public function definition()
    {
        return [
            'section_1_image_url'             => $this->faker->url,
            'section_1_alt_text'              => Str::random(10),
            'section_1_intro_text'            => Str::random(10),

            'section_2_background'            => Str::random(10),

            'section_3_h1'                    => Str::random(10),
            'section_3_h2'                    => Str::random(10),
            'section_3_background'            => Str::random(10),
            'section_3_button_text'           => Str::random(10),
            'section_3_button_color'          => Str::random(10),
            'section_3_button_url'            => $this->faker->url,
            'section_3_text'                  => Str::random(10),
            'section_3_target_blanc'          => $this->faker->boolean,

            'section_4_h2'                    => Str::random(10),

            'section_5_h2'                    => Str::random(10),
            'section_5_h3'                    => Str::random(10),
            'section_5_background'            => Str::random(10),
            'section_5_text'                  => Str::random(10),

            'section_6_h1'                    => Str::random(10),
            'section_6_h3'                    => Str::random(10),
            'section_6_button_text'           => Str::random(10),
            'section_6_button_color'          => Str::random(10),
            'section_6_button_url'            => $this->faker->url,
            'section_6_target_blanc'          => $this->faker->boolean,
            'section_6_text'                  => Str::random(10),
            'section_6_image_url'             => $this->faker->url,
            'section_6_alt_text'              => Str::random(10),

            'section_7_h2'                    => Str::random(10),

            'section_8_h1'                    => Str::random(10),
            'section_8_h3'                    => Str::random(10),
            'section_8_background'            => Str::random(10),
            'section_8_text'                  => Str::random(10),

            'section_9_h2'                    => Str::random(10),

            'section_10_h2'                   => Str::random(10),
            'section_10_h3'                   => Str::random(10),
            'section_10_text'                 => Str::random(10),
            'section_10_image_url'            => $this->faker->url,
            'section_10_alt_text'             => Str::random(10),

            'section_11_h2'                   => Str::random(10),
            'section_11_h3'                   => Str::random(10),
            'section_11_text'                 => Str::random(10),
            'section_11_button_text'          => Str::random(10),
            'section_11_button_url'           => $this->faker->url,
            'section_11_button_color'         => Str::random(10),
            'section_11_target_blanc'         => $this->faker->boolean,
            'section_11_image_url'            => $this->faker->url,
            'section_11_alt_text'             => Str::random(10),

            'section_12_h2'                   => Str::random(10),
            'section_12_h3'                   => Str::random(10),
            'section_12_media_1_image_url'    => $this->faker->url,
            'section_12_media_1_url'          => $this->faker->url,
            'section_12_media_1_traget_blanc' => $this->faker->boolean,
            'section_12_media_2_image_url'    => $this->faker->url,
            'section_12_media_2_url'          => $this->faker->url,
            'section_12_media_2_traget_blanc' => $this->faker->boolean,
            'section_12_media_3_image_url'    => $this->faker->url,
            'section_12_media_3_url'          => $this->faker->url,
            'section_12_media_3_traget_blanc' => $this->faker->boolean,
            'section_12_media_4_image_url'    => $this->faker->url,
            'section_12_media_4_url'          => $this->faker->url,
            'section_12_media_4_traget_blanc' => $this->faker->boolean,
            'section_12_media_5_image_url'    => $this->faker->url,
            'section_12_media_5_url'          => $this->faker->url,
            'section_12_media_5_traget_blanc' => $this->faker->boolean,
            'section_12_media_6_image_url'    => $this->faker->url,
            'section_12_media_6_url'          => $this->faker->url,
            'section_12_media_6_traget_blanc' => $this->faker->boolean,
        ];
    }
}

