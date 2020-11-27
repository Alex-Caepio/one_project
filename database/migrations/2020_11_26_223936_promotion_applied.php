<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PromotionApplied extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotions', static function(Blueprint $table) {
            $table->enum('applied_to', ['host', 'both'])->nullable(false)->default('both');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotions', static function(Blueprint $table) {
            $table->dropColumn(['applied_to']);
        });
    }
}
