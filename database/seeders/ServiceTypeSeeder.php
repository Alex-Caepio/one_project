<?php
namespace Database\Seeders;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('service_types')->delete();
        $data = [
            ['id' => 'workshop',         'name' => 'Workshop'],
            ['id' => 'appointment',      'name' => 'Appointment'],
            ['id' => 'bespoke',          'name' => 'Bespoke'],
            ['id' => 'events',           'name' => 'Events'],
            ['id' => 'retreat',          'name' => 'Retreat'],
//            ['id' => 'class_ad_hoc',     'name' => 'Class Ad Hoc'],
//            ['id' => 'class',            'name' => 'Class'],
//            ['id' => 'training_program', 'name' => 'Training Program'],
//            ['id' => 'econtent',         'name' => 'E-content'],
//            ['id' => 'product',          'name' => 'Product'],
        ];
        DB::table('service_types')->insert($data);
    }
}
