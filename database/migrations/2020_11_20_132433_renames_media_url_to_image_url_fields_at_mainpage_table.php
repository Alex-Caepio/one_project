<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamesMediaUrlToImageUrlFieldsAtMainpageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('main_pages', function (Blueprint $table) {
            $table->renameColumn('section_12_media_1_media_url', 'section_12_media_1_image_url');
            $table->renameColumn('section_12_media_2_media_url', 'section_12_media_2_image_url');
            $table->renameColumn('section_12_media_3_media_url', 'section_12_media_3_image_url');
            $table->renameColumn('section_12_media_4_media_url', 'section_12_media_4_image_url');
            $table->renameColumn('section_12_media_5_media_url', 'section_12_media_5_image_url');
            $table->renameColumn('section_12_media_6_media_url', 'section_12_media_6_image_url');
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
            $table->renameColumn('section_12_media_1_image_url', 'section_12_media_1_media_url');
            $table->renameColumn('section_12_media_2_image_url', 'section_12_media_2_media_url');
            $table->renameColumn('section_12_media_3_image_url', 'section_12_media_3_media_url');
            $table->renameColumn('section_12_media_4_image_url', 'section_12_media_4_media_url');
            $table->renameColumn('section_12_media_5_image_url', 'section_12_media_5_media_url');
            $table->renameColumn('section_12_media_6_image_url', 'section_12_media_6_media_url');
        });
    }
}
