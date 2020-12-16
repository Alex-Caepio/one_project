<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamesMainpagesSection12H3FieldsToSection12Text extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('main_pages', function (Blueprint $table) {
            $table->renameColumn('section_12_h3', 'section_12_text');
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
            $table->renameColumn('section_12_text', 'section_12_h3');
        });
    }
}
