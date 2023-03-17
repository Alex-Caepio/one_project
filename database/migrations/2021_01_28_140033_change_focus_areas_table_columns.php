<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFocusAreasTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('focus_areas', function (Blueprint $table) {
            $table->dropColumn('section_7_image_url');
            $table->dropColumn('section_7_video_url');
            $table->dropColumn('section_7_text');
            $table->string('section_7_textarea')->nullable()->after('section_7_background');
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
            $table->dropColumn('section_7_textarea');
        });
    }
}
