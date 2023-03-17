<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ScheduleFreezePractitioner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'schedule_freezes',
            static function (Blueprint $table) {
                $table->unsignedInteger('practitioner_id')->nullable(true);
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'schedule_freezes',
            static function (Blueprint $table) {
                $table->dropColumn('practitioner_id');
            }
        );
    }
}
