<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PractitionerCommission;
use Carbon\Carbon;

class PractitionerCommissionFactory extends Factory
{
    protected $model = PractitionerCommission::class;

    public function definition()
    {
        return [
            'practitioner_id'   => $this->faker->randomNumber(3),
            'rate'              => $this->faker->randomNumber(2),
            'date_from'         => Carbon::tomorrow()->format('Y-m-d'),
            'date_to'           => Carbon::tomorrow()->addDay()->format('Y-m-d'),
            'is_dateless'       => $this->faker->boolean(),
            'created_at'        => $this->faker->date("Y-m-d H:i:s"),
            'updated_at'        => $this->faker->date("Y-m-d H:i:s"),
        ];
    }
}
