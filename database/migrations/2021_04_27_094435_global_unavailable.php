<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GlobalUnavailable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user_unavailabilities', static function(Blueprint $table) {
            $table->id();
            $table->unsignedInteger('practitioner_id')->nullable(false);
            $table->dateTime('start_date')->nullable('false');
            $table->dateTime('end_date')->nullable('false');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('user_unavailabilities');
    }
}
