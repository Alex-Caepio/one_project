<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsVideoUrlFieldsToMainpageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('main_pages', function (Blueprint $table) {
            $table->renameColumn('section_6_media_url', 'section_6_image_url');
            $table->renameColumn('section_10_media_url', 'section_10_image_url');
            $table->renameColumn('section_11_media_url', 'section_11_image_url');

            $table->string('section_1_video_url')->after('section_1_image_url')->nullable();
            $table->string('section_6_video_url')->after('section_6_media_url')->nullable();
            $table->string('section_10_video_url')->after('section_10_media_url')->nullable();
            $table->string('section_11_video_url')->after('section_11_media_url')->nullable();
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
            $table->dropColumn([
                'section_1_video_url',
                'section_6_video_url',
                'section_10_video_url',
                'section_11_video_url'
            ]);

            $table->renameColumn('section_6_image_url', 'section_6_media_url');
            $table->renameColumn('section_10_image_url', 'section_10_media_url');
            $table->renameColumn('section_11_image_url', 'section_11_media_url');
        });
    }
}
