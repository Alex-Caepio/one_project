<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('is_installment')->nullable(false)->default(false);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('accepted_practitioner_agreement')->nullable(false)->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('is_installment');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('accepted_practitioner_agreement');
        });
    }
}
