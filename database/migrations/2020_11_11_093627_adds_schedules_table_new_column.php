<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsSchedulesTableNewColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->boolean('meals_breakfast')->nullable(false)->default(false);
            $table->boolean('meals_lunch')->nullable(false)->default(false);
            $table->boolean('meals_dinner')->nullable(false)->default(false);
            $table->boolean('meals_alcoholic_beverages')->nullable(false)->default(false);
            $table->boolean('meals_dietry_accomodated')->nullable(false)->default(false);
            $table->unsignedInteger('refund_terms')->nullable();
            $table->boolean('deposit_accepted')->nullable(false)->default(false);
            $table->double('deposit_amount')->nullable();
            $table->dateTime('deposit_final_date')->nullable();
            $table->text('booking_message')->nullable();
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
            $table->dropColumn(['meals_breakfast', 'meals_lunch', 'meals_dinner',
                'meals_alcoholic_beverages', 'meals_dietry_accomodated', 'refund_terms',
                'deposit_accepted', 'deposit_amount', 'deposit_final_date',
                'booking_message']);
        });
    }
}
