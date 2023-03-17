<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DiscountValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', static function(Blueprint $table) {
            $table->float('discount', 8, 2, true)->default(0);
        });
        Schema::table('purchases', static function(Blueprint $table) {
            $table->float('discount', 8, 2, true)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', static function(Blueprint $table) {
            $table->dropColumn('discount');
        });
        Schema::table('purchases', static function(Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
}
