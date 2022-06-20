<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Timezone;

class FixTimezoneValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Timezone::upsert([
            ['id' => 4,  'value' => '-09:30', 'label' => '(GMT -9:30) Taiohae'],
            ['id' => 10, 'value' => '-04:30', 'label' => '(GMT -4:30) Caracas'],
            ['id' => 12, 'value' => '-03:30', 'label' => '(GMT -3:30) Newfoundland'],
            ['id' => 20, 'value' => '+03:30', 'label' => '(GMT +3:30) Tehran'],
            ['id' => 22, 'value' => '+04:30', 'label' => '(GMT +4:30) Kabul'],
            ['id' => 24, 'value' => '+05:30', 'label' => '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi'],
            ['id' => 25, 'value' => '+05:45', 'label' => '(GMT +5:45) Kathmandu, Pokhar'],
            ['id' => 27, 'value' => '+06:30', 'label' => '(GMT +6:30) Yangon, Mandalay'],
            ['id' => 30, 'value' => '+08:45', 'label' => '(GMT +8:45) Eucla'],
            ['id' => 32, 'value' => '+09:30', 'label' => '(GMT +9:30) Adelaide, Darwin'],
            ['id' => 34, 'value' => '+10:30', 'label' => '(GMT +10:30) Lord Howe Island'],
            ['id' => 36, 'value' => '+11:30', 'label' => '(GMT +11:30) Norfolk Island'],
            ['id' => 38, 'value' => '+12:45', 'label' => '(GMT +12:45) Chatham Islands'],
        ], ['id'], ['value']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Timezone::upsert([
            ['id' => 4,  'value' => '-09:50', 'label' => '(GMT -9:30) Taiohae'],
            ['id' => 10, 'value' => '-04:50', 'label' => '(GMT -4:30) Caracas'],
            ['id' => 12, 'value' => '-03:50', 'label' => '(GMT -3:30) Newfoundland'],
            ['id' => 20, 'value' => '+03:50', 'label' => '(GMT +3:30) Tehran'],
            ['id' => 22, 'value' => '+04:50', 'label' => '(GMT +4:30) Kabul'],
            ['id' => 24, 'value' => '+05:50', 'label' => '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi'],
            ['id' => 25, 'value' => '+05:75', 'label' => '(GMT +5:45) Kathmandu, Pokhar'],
            ['id' => 27, 'value' => '+06:50', 'label' => '(GMT +6:30) Yangon, Mandalay'],
            ['id' => 30, 'value' => '+08:75', 'label' => '(GMT +8:45) Eucla'],
            ['id' => 32, 'value' => '+09:50', 'label' => '(GMT +9:30) Adelaide, Darwin'],
            ['id' => 34, 'value' => '+10:50', 'label' => '(GMT +10:30) Lord Howe Island'],
            ['id' => 36, 'value' => '+11:50', 'label' => '(GMT +11:30) Norfolk Island'],
            ['id' => 38, 'value' => '+12:75', 'label' => '(GMT +12:45) Chatham Islands'],
        ], ['id'], ['value']);
    }
}
