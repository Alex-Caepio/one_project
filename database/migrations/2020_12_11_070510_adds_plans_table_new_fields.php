<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsPlansTableNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('market_to_clients')->nullable(false)->default(false)->change();
            $table->boolean('client_reviews')->nullable(false)->default(false)->change();
            $table->renameColumn('prioritised_serivce_search', 'prioritised_service_search');
            $table->renameColumn('busines_profile_page', 'business_profile_page');
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
            $table->dropColumn(['market_to_clients', 'client_reviews']);
            $table->renameColumn('prioritised_service_search', 'prioritised_serivce_search');
            $table->renameColumn('business_profile_page', 'busines_profile_page');
        });
    }

}
