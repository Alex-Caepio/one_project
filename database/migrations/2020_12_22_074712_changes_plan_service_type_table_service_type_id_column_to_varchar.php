<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangesPlanServiceTypeTableServiceTypeIdColumnToVarchar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_service_type', function (Blueprint $table) {
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
        Schema::table('plan_service_type', function (Blueprint $table) {
            $table->unsignedInteger('service_type_id')->change();
        });
    }
}
