<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsSectionsColumnsToFocusAreas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('focus_areas', function (Blueprint $table) {
            $table->string('section_2_h2')->nullable();
            $table->string('section_2_h3')->nullable();
            $table->string('section_2_background')->nullable();
            $table->text('section_2_textarea')->nullable();

            $table->string('section_3_h2')->nullable();

            $table->string('section_4_tag_line')->nullable();
            $table->string('section_4_alt_text')->nullable();
            $table->string('section_4_url')->nullable();
            $table->boolean('section_4_target_blanc')->nullable();

            $table->string('section_5_h2')->nullable();
            $table->string('section_5_h3')->nullable();
            $table->string('section_5_background')->nullable();
            $table->text('section_5_textarea')->nullable();

            $table->string('section_6_header_h2')->nullable();

            $table->string('section_7_h2')->nullable();
            $table->string('section_7_h3')->nullable();
            $table->string('section_7_background')->nullable();
            $table->text('section_7_text')->nullable();

            $table->string('section_8_h2')->nullable();

            $table->string('section_9_tag_line')->nullable();
            $table->string('section_9_alt_text')->nullable();
            $table->string('section_9_url')->nullable();
            $table->boolean('section_9_target_blanc')->nullable();

            $table->string('section_10_h2')->nullable();
            $table->string('section_10_h3')->nullable();
            $table->string('section_10_background')->nullable();
            $table->text('section_10_textarea')->nullable();

            $table->string('section_11_h2')->nullable();

            $table->string('section_12_h2')->nullable();
            $table->string('section_12_h4')->nullable();

            $table->string('section_13_tag_line')->nullable();
            $table->string('section_13_alt_text')->nullable();
            $table->string('section_13_url')->nullable();
            $table->boolean('section_13_target_blanc')->nullable();
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
            $table->dropColumn(['section_2_h2', 'section_2_h3', 'section_2_background',
                'section_2_textarea', 'section_3_h2', 'section_4_tag_line', 'section_4_alt_text',
                'section_4_url', 'section_4_target_blanc', 'section_5_h2', 'section_5_h3',
                'section_5_background', 'section_5_textarea', 'section_6_header_h2', 'section_7_h2',
                'section_7_h3', 'section_7_background', 'section_7_text', 'section_8_h2', 'section_9_tag_line',
                'section_9_alt_text', 'section_9_url', 'section_9_target_blanc', 'section_10_h2',
                'section_10_h3', 'section_10_background', 'section_10_textarea', 'section_11_h2',
                'section_12_h2', 'section_12_h4', 'section_13_tag_line', 'section_13_alt_text',
                'section_13_url', 'section_13_target_blanc']);
        });
    }
}
