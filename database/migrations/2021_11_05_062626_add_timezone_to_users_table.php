<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimezoneToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timezones', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('timezone_id')->default(16); // London +0
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('timezones', function (Blueprint $table) {
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['timezone_id']);
            $table->dropColumn('timezone_id');
        });
    }
}
