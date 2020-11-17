<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsSchedulesTableMoreNewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedInteger('notice_min_time')->nullable();
            $table->enum('notice_min_period', ['mins', 'hours', 'days'])->nullable();
            $table->unsignedInteger('buffer_time')->nullable();
            $table->enum('buffer_period', ['mins', 'hours', 'days'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['notice_min_time', 'notice_min_period',
                'buffer_time', 'buffer_period']);
        });
    }
}
