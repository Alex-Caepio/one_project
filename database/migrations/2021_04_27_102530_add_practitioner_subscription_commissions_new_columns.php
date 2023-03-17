<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPractitionerSubscriptionCommissionsNewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('practitioner_subscription_commissions', function (Blueprint $table) {
            $table->string('stripe_coupon_id')->nullable();
            $table->string('subscription_schedule_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('practitioner_subscription_commissions', function (Blueprint $table) {
            $table->dropColumn(['stripe_coupon_id','subscription_schedule_id']);
        });
    }
}
