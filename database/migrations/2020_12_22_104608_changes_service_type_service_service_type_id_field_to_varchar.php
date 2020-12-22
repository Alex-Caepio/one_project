<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangesServiceTypeServiceServiceTypeIdFieldToVarchar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_type_service', function (Blueprint $table) {
            $table->string('service_type_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_type_service', function (Blueprint $table) {
            $table->unsignedInteger('service_type_id')->change();
        });
    }
}
