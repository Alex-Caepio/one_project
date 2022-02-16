<?php

use Illuminate\Database\Migrations\Migration;

class AddScheduleTypeToRescheduleRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `reschedule_requests` CHANGE `requested_by` `requested_by` ENUM(
            'practitioner',
            'client',
            'schedule'
        );");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `reschedule_requests` CHANGE `requested_by` `requested_by` ENUM(
            'practitioner',
            'client'
        );");
    }
}
