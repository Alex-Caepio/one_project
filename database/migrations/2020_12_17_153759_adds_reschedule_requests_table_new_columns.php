<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsRescheduleRequestsTableNewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reschedule_requests', function (Blueprint $table) {
            $table->string('old_location_displayed')->nullable();
            $table->string('new_location_displayed')->nullable();
            $table->dateTime('old_start_date')->nullable();
            $table->dateTime('new_start_date')->nullable();
            $table->dateTime('old_end_date')->nullable();
            $table->dateTime('new_end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reschedule_requests', function (Blueprint $table) {
            $table->dropColumn(['old_location_displayed', 'new_location_displayed',
            'old_start_date', 'new_start_date', 'old_end_date', 'new_end_date']);
        });
    }
}
