<?php


namespace Database\Factories;


use App\Models\Discipline;
use Illuminate\Database\Eloquent\Factories\Factory;


class DisciplineFactory extends Factory
{

    protected $model = Discipline::class;

    public function definition()
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
        return [
            'name' => $this->faker->randomElement($disciplines),
            'url' => $this->faker->url,
        ];
    }
}
