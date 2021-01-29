<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAreasTableColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('focus_areas', function (Blueprint $table) {
            $table->renameColumn('section_6_header_h2', 'section_6_h2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('focus_areas', function (Blueprint $table) {
            $table->renameColumn('section_6_h2', 'section_6_header_h2');
        });
    }
}
