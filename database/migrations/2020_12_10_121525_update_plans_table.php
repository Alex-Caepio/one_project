<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('schedules_per_service')->nullable()->change();
            $table->unsignedInteger('pricing_options_per_service')->nullable()->change();
            $table->boolean('market_to_clients')->nullable(false)->default(false)->change();
            $table->boolean('client_reviews')->nullable(false)->default(false)->change();
            $table->boolean('schedules_per_service_unlimited')->nullable(false)->default(false)->after('pricing_options_per_service');
            $table->boolean('pricing_options_per_service_unlimited')->nullable(false)->default(false)->after('pricing_options_per_service');
            $table->unsignedInteger('amount_bookings')->nullable()->after('unlimited_bookings');
            $table->boolean('discount_codes')->nullable(false)->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->enum('schedules_per_service', ['One', 'Unlimited'])->change();
            $table->enum('pricing_options_per_service', ['One', 'Unlimited'])->change();
            $table->string('market_to_clients')->change();
            $table->string('client_reviews')->change();
        });
    }
}
