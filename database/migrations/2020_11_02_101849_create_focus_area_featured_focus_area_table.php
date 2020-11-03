<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFocusAreaFeaturedFocusAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('focus_area_featured_focus_area', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('parent_focus_area_id');
            $table->unsignedInteger('child_focus_area_id');
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
        Schema::dropIfExists('focus_area_featured_focus_area');
    }
}
