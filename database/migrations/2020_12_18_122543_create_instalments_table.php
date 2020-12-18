<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstalmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instalments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('schedule_id')->nullable(false);
            $table->unsignedInteger('price_id')->nullable();
            $table->unsignedInteger('user_id')->nullable(false);
            $table->unsignedInteger('purchase_id')->nullable(false);
            $table->dateTime('payment_date')->nullable(false);
            $table->tinyInteger('is_paid')->nullable(false)->default(false);
            $table->double('payment_amount')->nullable();
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('instalments');
    }
}
