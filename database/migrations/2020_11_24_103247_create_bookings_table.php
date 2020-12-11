<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable(false);
            $table->unsignedInteger('schedule_id')->nullable(false);
            $table->unsignedInteger('price_id')->nullable(false);
            $table->unsignedInteger('availability_id')->nullable();
            $table->dateTime('datetime_from')->nullable();
            $table->dateTime('datetime_to')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->dateTime('created_at')->nullable(false);
            $table->dateTime('updated_at')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
