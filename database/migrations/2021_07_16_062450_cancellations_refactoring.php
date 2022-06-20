<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CancellationsRefactoring extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('transfers', static function(Blueprint $table) {
            $table->unsignedBigInteger('purchase_id')->nullable(true)->index('transfer_purchase_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('transfers', static function(Blueprint $table) {
            $table->dropColumn('purchase_id');
        });
    }
}
