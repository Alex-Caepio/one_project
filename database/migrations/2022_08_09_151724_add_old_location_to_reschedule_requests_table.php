<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOldLocationToRescheduleRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reschedule_requests', function (Blueprint $table) {
            $table->string('old_location', 255)->nullable()->after('new_location_displayed');
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
            $table->dropColumn('old_location');
        });
    }
}
