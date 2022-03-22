<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransferReversedIdToTransfers extends Migration
{

    public function up()
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->string('stripe_transfer_reversal_id', 255)->nullable();
        });
    }

    public function down()
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn('stripe_transfer_reversal_id');
        });
    }
}
