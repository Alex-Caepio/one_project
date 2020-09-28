<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        $this->call(CustomEmailSeeder::class);
        $this->call(TimezoneSeeder::class);
        //factory(\App\Models\CustomEmail::class,50)->create();
    }
}
