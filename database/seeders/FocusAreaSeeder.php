<?php

namespace Database\Seeders;

use App\Models\FocusArea;
use Illuminate\Database\Seeder;

class FocusAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FocusArea::factory()
               ->times(10)
               ->create();
    }
}
