<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsRescheduleRequestsTableNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reschedule_requests', function (Blueprint $table) {
            $table->unsignedInteger('booking_id')->nullable();
            $table->dateTime('new_datetime_from');
            $table->unsignedInteger('new_price_id')->nullable();
            $table->text('comment')->nullable();
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
            $table->dropColumn(['booking_id', 'new_datetime_from', 'new_price_id', 'comment']);
        });
    }
}
