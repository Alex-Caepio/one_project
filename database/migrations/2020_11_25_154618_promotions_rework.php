<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PromotionsRework extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Unique index for
        Schema::table('promotion_codes', static function(Blueprint $table) {
            $table->unique('name');
            $table->enum('status', ['active', 'disabled', 'deleted', 'complete'])->nullable(false)->default('disabled');
        });

        Schema::table('promotions', static function(Blueprint $table) {
            $table->dropColumn(['discipline_id', 'focus_area_id']);
            $table->string('service_type_id')->nullable()->change();
        });

        Schema::create('promotion_practitioner', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('promotion_id');
            $table->unsignedInteger('practitioner_id');
            $table->timestamps();
        });

        Schema::create('promotion_discipline', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('promotion_id');
            $table->unsignedInteger('discipline_id');
            $table->timestamps();
        });

        Schema::create('promotion_focus_area', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('promotion_id');
            $table->unsignedInteger('focus_area_id');
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
        Schema::table('promotion_codes', static function(Blueprint $table) {
            $table->dropUnique('name');
            $table->dropColumn('status');
        });

        Schema::table('promotions', static function(Blueprint $table) {
            $table->integer('discipline_id')->nullable();
            $table->integer('focus_area_id')->nullable();
            $table->integer('service_type_id')->nullable()->change();
        });

        Schema::dropIfExists('promotion_focus_area');
        Schema::dropIfExists('promotion_discipline');
        Schema::dropIfExists('promotion_practitioner');

    }
}
