<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDisciplinesTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $disciplines = [
            'Acupressure', 'Acupuncture', 'Alexander Therapy',
            'Aromatherapy', 'Ayurveda', 'Bodywork Therapy',
            'Bowen Therapy', 'Buddhism', 'Chakra Therapy',
            'Colour Therapy', 'Craniosacral Therapy', 'Crystal Healing',
            'Cupping Therapy', 'Ear Candling', 'Health Kinesiology',
            'Herbalism', 'Hermetic Philosophy', 'Homeopathy', 'Hypnotherapy',
            'Iridology', 'Life Coaching', 'Massage Therapy', 'Meditation',
            'Mindfulness', 'Naturopathy', 'Neuro Linguistic Programming',
            'Nutritional Therapy', 'Polarity Therapy', 'Pranic Healing',
            'Psychology', 'Qigong', 'Reflexology', 'Reiki', 'Shamanism',
            'Sound Therapy', 'Tai Chi', 'Taoism', 'Tapping', 'Traditional Chinese Medicine',
            'Western Herbal Medicine', 'Yoga', 'Zen Philosophy',
        ];

        Schema::create('disciplines', function (Blueprint $table) use ($disciplines) {
            $table->id();
            $table->enum('name', $disciplines);
            $table->string('url')->nullable();
            $table->boolean('is_published')->nullable();
            $table->timestamps();
        });
//        DB::statement('CREATE FULLTEXT INDEX disciplines_title_index
//        ON disciplines(title)');

    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disciplines');
    }
}
