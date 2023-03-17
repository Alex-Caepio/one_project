<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AmmendCostColumnToFloat extends Migration
{
    const TABLE_PRICES = 'prices';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE_PRICES, function(Blueprint $table){
            $table->float('cost', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(self::TABLE_PRICES, function(Blueprint $table){
            $table->integer('cost')->change();
        });
    }
}
