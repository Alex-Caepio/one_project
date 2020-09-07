<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_published')->nullable();
            $table->string('plan_id')->nullable();
            $table->dateTime('plan_until')->nullable();
            $table->enum('account_type',['user','practitioner']);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('stripe_id')->nullable();
            $table->text('about_me')->nullable();
            $table->boolean('emails_holistify_update')->nullable();
            $table->boolean('emails_practitioner_offers')->nullable();
            $table->boolean('email_forward_practitioners')->nullable();
            $table->boolean('email_forward_clients')->nullable();
            $table->boolean('email_forward_support')->nullable();
            $table->text('about_my_business')->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_address')->nullable();
            $table->string('business_email')->nullable();
            $table->string('public_link')->nullable();
            $table->string('business_introduction')->nullable();
            $table->enum('gender',['male','female'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('business_phone_number')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_admin')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
