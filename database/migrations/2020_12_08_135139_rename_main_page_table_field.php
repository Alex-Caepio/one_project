<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameMainPageTableField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('main_pages', function (Blueprint $table) {
            $table->renameColumn('section_12_media_1_traget_blanc', 'section_12_media_1_target_blanc');
            $table->renameColumn('section_12_media_2_traget_blanc', 'section_12_media_2_target_blanc');
            $table->renameColumn('section_12_media_3_traget_blanc', 'section_12_media_3_target_blanc');
            $table->renameColumn('section_12_media_4_traget_blanc', 'section_12_media_4_target_blanc');
            $table->renameColumn('section_12_media_5_traget_blanc', 'section_12_media_5_target_blanc');
            $table->renameColumn('section_12_media_6_traget_blanc', 'section_12_media_6_target_blanc');
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
            $table->renameColumn('section_12_media_1_target_blanc', 'section_12_media_1_traget_blanc');
            $table->renameColumn('section_12_media_2_target_blanc', 'section_12_media_2_traget_blanc');
            $table->renameColumn('section_12_media_3_target_blanc', 'section_12_media_3_traget_blanc');
            $table->renameColumn('section_12_media_4_target_blanc', 'section_12_media_4_traget_blanc');
            $table->renameColumn('section_12_media_5_target_blanc', 'section_12_media_5_traget_blanc');
            $table->renameColumn('section_12_media_6_target_blanc', 'section_12_media_6_traget_blanc');
        });
    }
}
