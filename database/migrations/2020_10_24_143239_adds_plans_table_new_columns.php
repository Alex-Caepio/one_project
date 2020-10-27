<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsPlansTableNewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->text('description')->after('name')->nullable();
            $table->string('image_url')->after('name')->nullable();
            $table->boolean('is_free')->after('price')->nullable();
            $table->boolean('contact_clients_with_booking')->nullable();
            $table->string('market_to_clients')->nullable();
            $table->string('client_reviews')->nullable();
            $table->integer('article_publishing')->nullable()->unsigned();
            $table->boolean('article_publishing_unlimited')->nullable();
            $table->boolean('prioritised_business_profile_search')->nullable();
            $table->boolean('prioritised_serivce_search')->nullable();
            $table->boolean('busines_profile_page')->nullable()->default(true);
            $table->boolean('unique_web_address')->nullable()->default(true);
            $table->boolean('onboarding_support')->nullable();
            $table->boolean('client_analytics')->nullable();
            $table->boolean('service_analytics')->nullable();
            $table->boolean('financial_analytics')->nullable();
            DB::statement('alter table plans modify commission_on_sale DOUBLE(5,2) DEFAULT 0');
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
            $table->dropColumn([
                'description', 'image_url', 'is_free', 'contact_clients_with_booking',
                'market_to_clients', 'client_reviews', 'article_publishing', 'article_publishing_unlimited',
                'prioritised_business_profile_search', 'prioritised_serivce_search', 'busines_profile_page',
                'unique_web_address', 'onboarding_support', 'client_analytics', 'service_analytics',
                'financial_analytics'
            ]);
            DB::statement('alter table plans modify commission_on_sale varchar(255)');
        });
    }
}
