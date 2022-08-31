<?php

use App\Models\CustomEmail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCustomRetreatEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        CustomEmail::where('id', 130)->first()->delete();
        CustomEmail::where('id', 131)->first()->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        CustomEmail::where('id', 130)->first()->delete();
        CustomEmail::where('id', 131)->first()->delete();
    }
}
