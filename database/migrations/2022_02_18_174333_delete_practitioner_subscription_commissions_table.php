<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeletePractitionerSubscriptionCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('practitioner_subscription_commissions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('practitioner_subscription_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->integer('rate');
            $table->dateTime('date_from')->nullable();
            $table->dateTime('date_to')->nullable();
            $table->boolean('is_dateless')->default(false);
            $table->dateTime('created_at')->default(now());
            $table->dateTime('updated_at')->default(now());
            $table->string('stripe_coupon_id')->nullable();
            $table->string('subscription_schedule_id')->nullable();
        });
    }
}
