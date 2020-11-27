<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;

class PromotionsSoftdeletes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotions', static function(Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('promotion_codes', static function(Blueprint $table) {
            $table->softDeletes();
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
            $table->dropSoftDeletes();
        });
        Schema::table('promotion_codes', static function(Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
