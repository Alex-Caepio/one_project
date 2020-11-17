<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsPricesTableNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->boolean('is_free')->default(false);
            $table->timestamp('available_till')->nullable();
            $table->unsignedInteger('min_purchase')->nullable();
            $table->unsignedInteger('number_available')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn(['name', 'is_free', 'available_till',
                'min_purchase', 'number_available']);
        });
    }
}
