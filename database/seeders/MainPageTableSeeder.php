<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MainPage;

class MainPageTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MainPage::factory()->create();
    }
}
