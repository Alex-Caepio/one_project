<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LastPublishedField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', static function(Blueprint $table) {
            $table->dateTime('last_published')->nullable();
        });

        Schema::table('services', static function(Blueprint $table) {
            $table->dateTime('last_published')->nullable();
        });

        DB::statement("UPDATE `articles` SET `last_published`=`published_at`;");
        DB::statement("UPDATE `services` SET `last_published`=`published_at`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('articles', static function(Blueprint $table) {
            $table->dropColumn('last_published');
        });

        Schema::table('services', static function(Blueprint $table) {
            $table->dropColumn('last_published');
        });
    }
}
