<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsRepeatColumsToSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->enum('repeat', ['daily', 'weekly', 'monthly'])->nullable();
            $table->unsignedInteger('repeat_every')->nullable();
            $table->enum('repeat_period', ['day', 'week', 'month'])->nullable();
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
            $table->dropColumn(['repeat', 'repeat_every', 'repeat_period']);
        });
    }
}
