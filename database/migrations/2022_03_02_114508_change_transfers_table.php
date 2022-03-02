<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTransfersTable extends Migration
{
    const TABLE_TRANSFERS = 'transfers';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE_TRANSFERS, function(Blueprint $table){
            $table->float('amount', 10, 2)->change();
            $table->float('amount_original', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(self::TABLE_TRANSFERS, function(Blueprint $table){
            $table->integer('amount')->change();
            $table->integer('amount_original')->change();
        });
    }
}
