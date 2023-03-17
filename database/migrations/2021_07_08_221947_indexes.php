<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Indexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', static function(Blueprint $table) {
            $table->index('service_type_id');
            $table->index('is_published');
        });

        Schema::table('schedules', static function(Blueprint $table) {
            $table->index('start_date');
            $table->index('end_date');
            $table->index('is_published');
        });

        DB::statement('ALTER TABLE `services` ADD FULLTEXT text_columns(`title`);');
        DB::statement('ALTER TABLE `services` ADD FULLTEXT text_intro(`introduction`);');
        DB::statement('ALTER TABLE `services` ADD FULLTEXT text_desc(`description`);');
        DB::statement('ALTER TABLE `focus_areas` ADD FULLTEXT text_fname(`name`);');
        DB::statement('ALTER TABLE `disciplines` ADD FULLTEXT text_dname(`name`);');
        DB::statement('ALTER TABLE `keywords` ADD FULLTEXT text_ktitle(`title`);');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
