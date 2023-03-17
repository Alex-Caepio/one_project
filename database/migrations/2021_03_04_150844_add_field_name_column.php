<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldNameColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_files', function (Blueprint $table) {
            $table->string('name')->nullable();
        });

        Schema::table('schedule_hidden_files', function (Blueprint $table) {
            $table->string('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_files', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('schedule_hidden_files', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
}
