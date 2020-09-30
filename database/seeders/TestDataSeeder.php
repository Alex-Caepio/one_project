<?php

use App\Models\Article;
use App\Models\Discipline;
use App\Models\Keyword;
use App\Models\Location;
use App\Models\Price;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @return void
     */
    public function run()
    {
        $user = $this->createUser();
        $services = $this->createServices($user, 50);
        $this->createArticles($user, 10);
        $locations = $this->createLocations(2);
        $keywords = $this->createKeywords();
        $disciplines = $this->createDisciplines();
        $this->attachDisciplinesToServices($disciplines, $services);
        $this->attachKeywordsToServices($keywords, $services);
        $schedules = $this->createSchedules($services, $locations);
        $this->createPrices($schedules);
    }

    private function createUser(): User
    {
//        Bearer 1|PaRXix95obA9fYRs2ir5RDTmfqFdW9lCsQ7ASqkcaZIByyqdXdSJSrFJaiTBWYEu4ORZKHeKK03UQkln
        $user = User::factory()->create(['email' => 'testmail@mail.com']);
        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'name' => 'access-token',
            'token' => '39ca2fe1f605a9f258cf9088c86a61e2f5078532b7ce900f2d703a7e2981c9ff',
            'abilities' => '["*"]',
        ]);

        return $user;
    }

    private function createServices(User $user, int $amount): Collection
    {
        return Service::factory($amount)->create(['user_id' => $user->id]);
    }

    private function createArticles(User $user, int $amount): Collection
    {
        return Article::factory($amount)->create([
            'user_id' => $user->id,
        ]);
    }

    private function createLocations(int $amount): Collection
    {
        return Location::factory($amount)->create();
    }

    private function createKeywords(): Collection
    {
        return Keyword::factory(4)->create();
    }

    private function createDisciplines(): Collection
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

        foreach ($disciplines as $discipline) {
            Discipline::factory()->create(['title' => $discipline]);
        }

        return Discipline::all();
    }

    private function attachDisciplinesToServices(Collection $disciplines, Collection $services): void
    {
        $services->each(fn(Service $service) => $service->disciplines()->attach($disciplines->random()));
    }

    private function attachKeywordsToServices(Collection $keywords, Collection $services): void
    {
        $services->each(static function ($service) use ($keywords) {
            $service->keywords()->attach($keywords->random(3));
        });
    }

    private function createSchedules(Collection $services, Collection $locations): Collection
    {
        foreach ($services as $key => $service) {
            Schedule::factory()->create([
                'service_id' => $service->id,
                'location_id' => $locations->random(),
            ]);
        }

        return Schedule::all();
    }

    private function createPrices($schedules): Collection
    {
        foreach ($schedules as $schedule) {
            Price::factory()->create([
                'schedule_id' => $schedule->id,
            ]);
        }

        return Price::all();
    }

}
