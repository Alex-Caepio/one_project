<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNotificationTable extends Migration
{
    const TABLE_NOTIFICATIONS = 'notifications';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE_NOTIFICATIONS, function(Blueprint $table){
            $table->float('price_payed', 10, 2)->change();
            $table->float('price_refunded', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(self::TABLE_NOTIFICATIONS, function(Blueprint $table){
            $table->integer('price_payed')->change();
            $table->integer('price_refunded')->change();
        });
    }
}
