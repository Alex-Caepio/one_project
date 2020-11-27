<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixPromotions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotions', static function(Blueprint $table) {
            $table->enum('status', ['active', 'disabled', 'deleted', 'complete'])->nullable(false)->default('disabled');
        });
        Schema::table('promotion_codes', static function(Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotion_codes', static function(Blueprint $table) {
            $table->enum('status', ['active', 'disabled', 'deleted', 'complete'])->nullable(false)->default('disabled');
        });
        Schema::table('promotions', static function(Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
