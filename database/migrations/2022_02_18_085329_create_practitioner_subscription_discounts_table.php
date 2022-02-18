<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePractitionerSubscriptionDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('practitioner_subscription_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('coupon_id')->comment('Stripe coupon_id');
            $table->string('subscription_id')->comment('Stripe subscription_id');
            $table->unsignedMediumInteger('rate')->comment('Stripe amount_off');
            $table->enum('duration_type', ['once', 'forever', 'repeating'])->comment('Stripe duration');
            $table->unsignedMediumInteger('duration_in_months')->nullable()->comment('Stripe duration_in_months');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('practitioner_subscription_discounts');
    }
}
