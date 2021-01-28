<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFocusAreasTableNewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('focus_areas', function (Blueprint $table) {
            $table->string('section_7_image_url')->nullable()->after('section_7_text');
            $table->string('section_7_video_url')->nullable()->after('section_7_text');

            $table->string('section_11_image_url')->nullable()->after('section_11_h2');
            $table->string('section_11_video_url')->nullable()->after('section_11_h2');

            $table->string('section_13_image_url')->nullable();
            $table->string('section_13_video_url')->nullable();

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
            $table->dropColumn(['section_7_image_url', 'section_7_video_url',
                'section_11_image_url', 'section_11_video_url',
                'section_13_image_url', 'section_13_video_url']);
        });
    }
}
