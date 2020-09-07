<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('stripe_id')->nullable();
            $table->double('price')->nullable();
            $table->boolean('unlimited_bookings')->nullable();
            $table->string('commission_on_sale')->nullable();

            $table->enum('schedules_per_service',['One','Unlimited'])->nullable();
            $table->enum('pricing_options_per_service',['One','Unlimited'])->nullable();
            $table->boolean('list_paid_services')->nullable();
            $table->boolean('list_free_services')->nullable();
            $table->boolean('take_deposits_and_instalment')->nullable();
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
        Schema::dropIfExists('plans');
    }
}
