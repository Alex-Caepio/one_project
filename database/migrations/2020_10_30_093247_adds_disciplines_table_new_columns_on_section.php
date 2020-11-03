<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsDisciplinesTableNewColumnsOnSection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->string('section_2_h2')->nullable();
            $table->string('section_2_h3')->nullable();
            $table->string('section_2_background')->nullable();
            $table->text('section_2_textarea')->nullable();

            $table->string('section_3_h2')->nullable();
            $table->string('section_3_h4')->nullable();

            $table->string('section_4_h2')->nullable();
            $table->string('section_4_h3')->nullable();
            $table->string('section_4_background')->nullable();
            $table->text('section_4_textarea')->nullable();

            $table->string('section_5_header_h2')->nullable();

            $table->string('section_6_h2')->nullable();
            $table->string('section_6_h3')->nullable();
            $table->string('section_6_background')->nullable();
            $table->text('section_6_textarea')->nullable();

            $table->string('section_7_media_url')->nullable();
            $table->string('section_7_tag_line')->nullable();
            $table->string('section_7_alt_text')->nullable();
            $table->string('section_7_url')->nullable();
            $table->boolean('section_7_target_blanc')->nullable();

            $table->string('section_8_h2')->nullable();

            $table->string('section_9_h2')->nullable();
            $table->string('section_9_h3')->nullable();
            $table->string('section_9_background')->nullable();
            $table->text('section_9_textarea')->nullable();

            $table->string('section_10_h2')->nullable();

            $table->string('section_11_media_url')->nullable();
            $table->string('section_11_tag_line')->nullable();
            $table->string('section_11_alt_text')->nullable();
            $table->string('section_11_url')->nullable();
            $table->boolean('section_11_target_blanc')->nullable();

            $table->string('section_12_h2')->nullable();

            $table->string('section_13_media_url')->nullable();
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
        Schema::table('disciplines', function (Blueprint $table) {
            $table->dropColumn(['section_2_h2', 'section_2_h3', 'section_2_background', 'section_2_textarea',
                'section_3_h2','section_3_h4','section_4_h2','section_4_h3','section_4_background',
                'section_4_textarea','section_5_header_h2','section_6_h2','section_6_h3','section_6_background',
                'section_6_textarea','section_7_media_url','section_7_tag_line','section_7_alt_text',
                'section_7_url','section_7_target_blanc','section_8_h2','section_9_h2','section_9_h3',
                'section_9_background','section_9_textarea','section_10_h2','section_11_media_url','section_11_tag_line',
                'section_11_alt_text','section_11_url','section_11_target_blanc','section_12_h2',
                'section_13_media_url','section_13_tag_line','section_13_alt_text','section_13_url','section_13_target_blanc']);
        });
    }
}
