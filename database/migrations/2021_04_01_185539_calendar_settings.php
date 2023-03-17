<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CalendarSettings extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('user_google_calendar', static function(Blueprint $table) {
            $table->unique('user_id');
            $table->unsignedInteger('timezone_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('user_google_calendar', static function(Blueprint $table) {
            $table->dropColumn('timezone_id');
            $table->dropUnique('user_id');
        });
    }
}
