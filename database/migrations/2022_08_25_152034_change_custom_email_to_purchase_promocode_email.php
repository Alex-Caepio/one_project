<?php

use App\Models\CustomEmail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCustomEmailToPurchasePromocodeEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        CustomEmail::upsert(
            [
                ['id' => 96, 'name' => 'Host Promo Used']
            ],
            ['id'],
            ['name']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        CustomEmail::upsert(
            [
                ['id' => 96, 'name' => 'Host Promo Used']
            ],
            ['id'],
            ['name']
        );
    }
}
