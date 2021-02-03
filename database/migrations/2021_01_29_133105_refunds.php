<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refunds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancellations', function(Blueprint $table) {
            $table->id();
            $table->unsignedInteger('purchase_id');
            $table->unsignedInteger('booking_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('practitioner_id');
            $table->boolean('cancelled_by_client')->default(0);
            $table->double('amount')->nullable();
            $table->double('fee')->nullable();
            $table->string('stripe_id', 50)->nullable();
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
        Schema::dropIfExists('cancellations');
    }
}
