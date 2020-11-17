<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_availabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('schedule_id')->nullable(false);
            $table->enum('days', ['everyday', 'weekdays', 'weekends', 'monday',
                'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable(false);
            $table->time('start_time')->nullable(false);
            $table->time('end_time')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_availabilities');
    }
}
