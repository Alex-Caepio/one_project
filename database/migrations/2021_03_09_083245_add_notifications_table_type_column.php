<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationsTableTypeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('type',  [
                'reschedule_declined_by_client',
                'reschedule_declined_by_practitioner',
                'reschedule_accepted_by_client',
                'reschedule_accepted_by_practitioner',
                'booking_canceled_by_client',
                'booking_canceled_by_practitioner',
            ])->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
