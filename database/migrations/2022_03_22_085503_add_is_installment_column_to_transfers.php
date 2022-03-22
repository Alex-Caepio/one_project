<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsInstallmentColumnToTransfers extends Migration
{
    public function up()
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->boolean('is_installment');
        });
    }

    public function down()
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn('is_installment');
        });
    }
}
