<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PurchaseChanges extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('purchases', function(Blueprint $table) {
            $table->string('reference')->nullable(true)->unique();
            $table->bigInteger('service_id', false, true);
            $table->bigInteger('promocode_id', false, true)->nullable(true);
            $table->unsignedInteger('schedule_id')->nullable(true)->change();
            $table->dropColumn('promocode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('purchases', function(Blueprint $table) {
            $table->dropColumn('reference');
            $table->dropColumn('service_id');
            $table->dropColumn('promocode_id');
            $table->unsignedInteger('schedule_id')->nullable(false)->change();
            $table->char('promocode')->nullable();
        });
    }
}
