<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Location;
class LocationsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Location::factory()
            ->times(50)
            ->create();
    }
}
