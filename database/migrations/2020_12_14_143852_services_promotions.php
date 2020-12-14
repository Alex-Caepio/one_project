<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ServicesPromotions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_service_type', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('promotion_id');
            $table->string('service_type_id');
            $table->timestamps();
        });

        Schema::table('promotions', function(Blueprint $table) {
            $table->dropColumn('service_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_service_type');

        Schema::table('promotions', function(Blueprint $table) {
            $table->string('service_type_id');
        });
    }
}
