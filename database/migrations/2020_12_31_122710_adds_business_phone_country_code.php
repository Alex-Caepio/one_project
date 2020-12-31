<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsBusinessPhoneCountryCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('business_phone_country_code')->nullable()->after('business_phone_number');
            $table->renameColumn('mobile_country_number_code', 'mobile_country_code');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('business_phone_country_code');
            $table->renameColumn('mobile_country_code', 'mobile_country_number_code');
        });
    }
}
