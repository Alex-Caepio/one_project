<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsSchedulesTableNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->string('post_code')->nullable()->after('country');
            $table->boolean('is_virtual')->nullable();
            $table->string('url')->nullable();
            $table->unsignedInteger('deposit_instalments')->nullable()->after('deposit_amount');
            $table->unsignedInteger('deposit_instalment_frequency')->nullable()->after('deposit_instalments');
            $table->boolean('book_full_series')->nullable();
            $table->enum('accomodation', ['included', 'optional', 'no'])->nullable();
            $table->string('accomodation_details')->nullable();
            $table->enum('travel', ['included', 'optional', 'no'])->nullable();
            $table->string('travel_details')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn([
                'post_code',
                'is_virtual',
                'url',
                'deposit_instalments',
                'deposit_instalment_frequency',
                'book_full_series',
                'accomodation',
                'accomodation_details',
                'travel',
                'travel_details',
            ]);
        });
    }
}
