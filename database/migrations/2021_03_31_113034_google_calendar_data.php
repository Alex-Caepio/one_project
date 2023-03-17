<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GoogleCalendarData extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user_google_calendar', static function(Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('user_id')->nullable(false);
            $table->string('access_token')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->string('refresh_token')->nullable();
            $table->string('calendar_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('user_google_calendar');
    }
}
