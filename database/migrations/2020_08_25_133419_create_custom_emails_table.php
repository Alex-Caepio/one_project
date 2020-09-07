<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_emails', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->enum('user_type',['client','practitioner','all'])->nullable();
            $table->string('from_email')->nullable();
            $table->string('from_title')->nullable();
            $table->string('subject')->nullable();
            $table->binary('logo')->nullable();
            $table->text('text')->nullable();
            $table->integer('delay')->nullable();
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
        Schema::dropIfExists('custom_emails');
    }
}
