<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main_pages', function (Blueprint $table) {
            $table->id();
            $table->string('section_1_image_url')->nullable();
            $table->string('section_1_alt_text')->nullable();
            $table->string('section_1_intro_text')->nullable();

            $table->string('section_2_background')->nullable();

            $table->string('section_3_h1')->nullable();
            $table->string('section_3_h2')->nullable();
            $table->string('section_3_background')->nullable();
            $table->string('section_3_button_text')->nullable();
            $table->string('section_3_button_color')->nullable();
            $table->string('section_3_button_url')->nullable();
            $table->text('section_3_text')->nullable();
            $table->boolean('section_3_target_blanc')->nullable();

            $table->string('section_4_h2')->nullable();
            $table->string('section_5_h2')->nullable();
            $table->string('section_5_h3')->nullable();
            $table->string('section_5_background')->nullable();
            $table->text('section_5_text')->nullable();
            $table->string('section_6_h1')->nullable();
            $table->string('section_6_h3')->nullable();
            $table->string('section_6_button_text')->nullable();
            $table->string('section_6_button_color')->nullable();
            $table->string('section_6_button_url')->nullable();
            $table->boolean('section_6_target_blanc')->nullable();
            $table->text('section_6_text')->nullable();
            $table->string('section_6_media_url')->nullable();
            $table->string('section_6_alt_text')->nullable();
            $table->string('section_7_h2')->nullable();
            $table->string('section_8_h1')->nullable();
            $table->string('section_8_h3')->nullable();
            $table->string('section_8_background')->nullable();
            $table->text('section_8_text')->nullable();
            $table->string('section_9_h2')->nullable();
            $table->string('section_10_h2')->nullable();
            $table->string('section_10_h3')->nullable();
            $table->text('section_10_text')->nullable();
            $table->string('section_10_media_url')->nullable();
            $table->string('section_10_alt_text')->nullable();
            $table->string('section_11_h2')->nullable();
            $table->string('section_11_h3')->nullable();
            $table->text('section_11_text')->nullable();
            $table->string('section_11_button_text')->nullable();
            $table->string('section_11_button_url')->nullable();
            $table->string('section_11_button_color')->nullable();
            $table->boolean('section_11_target_blanc')->nullable();
            $table->string('section_11_media_url')->nullable();
            $table->string('section_11_alt_text')->nullable();
            $table->string('section_12_h2')->nullable();
            $table->string('section_12_h3')->nullable();
            $table->string('section_12_media_1_media_url')->nullable();
            $table->string('section_12_media_1_url')->nullable();
            $table->boolean('section_12_media_1_traget_blanc')->nullable();
            $table->string('section_12_media_2_media_url')->nullable();
            $table->string('section_12_media_2_url')->nullable();
            $table->boolean('section_12_media_2_traget_blanc')->nullable();
            $table->string('section_12_media_3_media_url')->nullable();
            $table->string('section_12_media_3_url')->nullable();
            $table->boolean('section_12_media_3_traget_blanc')->nullable();
            $table->string('section_12_media_4_media_url')->nullable();
            $table->string('section_12_media_4_url')->nullable();
            $table->boolean('section_12_media_4_traget_blanc')->nullable();
            $table->string('section_12_media_5_media_url')->nullable();
            $table->string('section_12_media_5_url')->nullable();
            $table->boolean('section_12_media_5_traget_blanc')->nullable();
            $table->string('section_12_media_6_media_url')->nullable();
            $table->string('section_12_media_6_url')->nullable();
            $table->boolean('section_12_media_6_traget_blanc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('main_pages');
    }
}
