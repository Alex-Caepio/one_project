<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FreePlanPeriod extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('plans', static function(Blueprint $table) {
            $table->dateTime('free_start_from')->nullable(true);
            $table->dateTime('free_start_to')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('plans', static function(Blueprint $table) {
            $table->dropColumn('free_start_from');
            $table->dropColumn('free_start_to');
        });
    }
}
