<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->decimal('spend_min',19,4)->nullable();
            $table->decimal('spend_max',19,4)->nullable();
            $table->enum('discount_type',['percentage','fixed'])->nullable();
            $table->integer('discount_value')->nullable();
            $table->integer('service_type_id')->nullable();
            $table->integer('discipline_id')->nullable();
            $table->integer('focus_area_id')->nullable();
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
        Schema::dropIfExists('promotions');
    }
}
