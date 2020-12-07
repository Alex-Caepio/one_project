<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LostStatusPromocodeds extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::statement("ALTER TABLE `promotions` CHANGE `status` `status` ENUM('active', 'disabled', 'deleted', 'complete', 'expired') NOT NULL DEFAULT 'active';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::statement("UPDATE `promotions` SET `status` = 'disabled' WHERE `status` = 'éxpired';");
        DB::statement("ALTER TABLE `promotions` CHANGE `status` `status` ENUM('active', 'disabled', 'deleted', 'complete') NOT NULL DEFAULT `active`;");
    }
}
