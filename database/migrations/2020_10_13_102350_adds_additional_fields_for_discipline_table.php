<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsAdditionalFieldsForDisciplineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->string('banner_url')->nullable()->after('url');
            $table->string('icon_url')->nullable()->after('url');
            $table->string('introduction')->nullable()->after('name');
            $table->string('description')->nullable()->after('name');
            $table->string('name')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->dropColumn(['banner_url', 'icon_url', 'introduction', 'description']);
        });
    }
}
