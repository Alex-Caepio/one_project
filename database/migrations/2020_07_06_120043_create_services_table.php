<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('keyword_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->boolean('is_published')->nullable();
            $table->text('introduction')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('CREATE FULLTEXT INDEX service_title_index
        ON services(title)');
        DB::statement('CREATE FULLTEXT INDEX service_description_index
        ON services(description)');
        DB::statement('CREATE FULLTEXT INDEX service_introduction_index
        ON services(introduction)');
//        DB::statement('ALTER TABLE services ADD FULLTEXT (description)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
}
