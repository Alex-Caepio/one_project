<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('practitioner_id')->nullable();
            $table->unsignedInteger('receiver_id')->nullable(false);
            $table->string('old_address')->nullable();
            $table->string('new_address')->nullable();
            $table->dateTime('old_datetime')->nullable();
            $table->dateTime('new_datetime')->nullable();
            $table->unsignedInteger('price_id')->nullable();
            $table->unsignedInteger('price_payed')->nullable();
            $table->unsignedInteger('price_refunded')->nullable();
            $table->dateTime('read_at')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
