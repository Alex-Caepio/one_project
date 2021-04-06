<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->renameColumn('url', 'slug');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->renameColumn('url', 'slug');
        });

        Schema::table('focus_areas', function (Blueprint $table) {
            $table->renameColumn('url', 'slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->renameColumn('slug','url');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->renameColumn('slug','url');
        });

        Schema::table('focus_areas', function (Blueprint $table) {
            $table->renameColumn('slug','url');
        });
    }
}
