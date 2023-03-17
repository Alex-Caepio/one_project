<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FullyPaid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', static function(Blueprint $table) {
            $table->boolean('is_fully_paid')->nullable(false)->default(0);
        });
        DB::statement("UPDATE `bookings` SET `is_fully_paid` = 1 WHERE `is_installment` = 0;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', static function(Blueprint $table) {
            $table->dropColumn('is_fully_paid');
        });
    }
}
