<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RescheduleNoreply extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reschedule_requests', static function(Blueprint $table) {
            $table->dateTime('noreply_sent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reschedule_requests', static function(Blueprint $table) {
            $table->dropColumn('noreply_sent');
        });
    }
}
