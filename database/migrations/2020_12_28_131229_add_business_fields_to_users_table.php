<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->char('business_country')->nullable();
            $table->char('business_city')->nullable();
            $table->char('business_postal_code')->nullable();
            $table->char('business_time_zone')->nullable();
            $table->char('business_vat')->nullable();
            $table->char('business_company_houses_id')->nullable();
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
            $table->dropColumn([
                'business_country',
                'business_city',
                'business_postal_code',
                'business_time_zone',
                'business_vat',
                'business_company_houses_id'
            ]);
        });
    }
}
