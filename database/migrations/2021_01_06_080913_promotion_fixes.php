<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PromotionFixes extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        DB::statement("ALTER TABLE `promotions` CHANGE `discount_type` `discount_type` ENUM('monetary', 'fixed', 'percentage');");

        DB::statement("UPDATE `promotions` SET `discount_type` = 'monetary' WHERE `discount_type` = 'fixed';");

        DB::statement("ALTER TABLE `promotions` CHANGE `discount_type` `discount_type` ENUM('monetary', 'percentage');");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }
}
