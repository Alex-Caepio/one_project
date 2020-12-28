<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePractitionerCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('practitioner_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('practitioner_id')->nullable(false);
            $table->double('rate')->nullable(false);
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->boolean('is_dateless')->default(false);
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
        Schema::dropIfExists('practitioner_commissions');
    }
}
