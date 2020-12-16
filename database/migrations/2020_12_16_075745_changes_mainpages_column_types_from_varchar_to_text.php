<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangesMainpagesColumnTypesFromVarcharToText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('main_pages', function (Blueprint $table) {
            $table->text('section_3_h2')->change();
            $table->text('section_5_h3')->change();
            $table->text('section_5_h3')->change();
            $table->text('section_8_h3')->change();
            $table->text('section_10_h3')->change();
            $table->text('section_11_h3')->change();
            $table->text('section_12_h3')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('main_pages', function (Blueprint $table) {
            $table->string('section_3_h2')->change();
            $table->string('section_5_h3')->change();
            $table->string('section_5_h3')->change();
            $table->string('section_8_h3')->change();
            $table->string('section_10_h3')->change();
            $table->string('section_11_h3')->change();
            $table->string('section_12_h3')->change();
        });
    }
}
