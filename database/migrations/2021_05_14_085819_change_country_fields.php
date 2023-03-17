<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeCountryFields extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::table('users', static function(Blueprint $table) {
            $table->integer('country_id')->nullable()->after('country');
            $table->integer('business_country_id')->nullable()->after('business_country');
        });

        DB::statement("UPDATE `users` u INNER JOIN `countries` c ON (u.`country` = c.`nicename`) SET u.`country_id` = c.`id` WHERE u.`country` IS NOT NULL;");
        DB::statement("UPDATE `users` u INNER JOIN `countries` c ON (u.`business_country` = c.`nicename`) SET u.`business_country_id` = c.`id` WHERE u.`country` IS NOT NULL;");

        Schema::table('users', static function(Blueprint $table) {
            $table->dropColumn('country');
            $table->dropColumn('business_country');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

    }
}
