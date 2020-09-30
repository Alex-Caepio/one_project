<?php


namespace Database\Factories;


use App\Models\EmailMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailMessageFactory extends Factory
{
    protected $model = EmailMessage::class;

    public function definition()
    {
        return [
            'receiver_id' => $this->faker->randomNumber(3),
            'sender_id' => $this->faker->randomNumber(3),
            'text' => $this->faker->sentence(),
        ];
    }
}
