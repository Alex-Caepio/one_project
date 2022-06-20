<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotificationTypesExplicit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `notifications` CHANGE `type` `type`
ENUM('booking_canceled_by_client', 'booking_canceled_by_practitioner', 'amendment_canceled_by_client',
'declined_by_client', 'rescheduled_by_client', 'rescheduled_by_practitioner');");
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
