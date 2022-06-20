<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFocusAreasTableNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('focus_areas', function (Blueprint $table) {
            $table->string('section_4_video_url')->nullable()->after('section_4_target_blanc');
            $table->string('section_4_image_url')->nullable()->after('section_4_target_blanc');

            $table->string('section_9_video_url')->nullable()->after('section_9_target_blanc');
            $table->string('section_9_image_url')->nullable()->after('section_9_target_blanc');

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
            $table->dropColumn([
                'section_4_video_url', 'section_4_image_url',
                'section_9_video_url', 'section_9_image_url',
                'section_13_image_url', 'section_13_video_url'
            ]);
        });
    }
}
