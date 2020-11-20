<?php

namespace Tests\Api;

use App\Models\Discipline;
use App\Models\FocusArea;
use App\Models\Price;
use App\Models\Promotion;
use App\Models\PromotionCode;
use App\Models\Schedule;
use App\Models\ScheduleAvailabilities;
use App\Models\ScheduleFile;
use App\Models\ScheduleHiddenFile;
use App\Models\ScheduleUnavailabilities;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_all_schedule(): void
    {
        Schedule::factory()->count(2)->create();
        $service = Service::factory()->create();
        $response = $this->json('get', "/api/services/{$service->id}/schedules");

        $response
            ->assertOk();
    }

    public function test_store_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 1]);
        $schedule = Schedule::factory()->make();
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'start_date' => $schedule->start_date,
            'end_date' => $schedule->end_date,
            'cost' => $schedule->cost,
            'madia_files' => [
                ['url' => 'http://google.com'],
                ['url' => 'http://google.com'],
            ],
        ]);
        $response->assertOk();
    }

    public function test_all_user()
    {
        $schedule = Schedule::factory()->create();
        $user = User::factory()->create();
        $promotion_code = PromotionCode::factory()->create();
        $response = $this->json('get', "api/schedule/{$schedule->id}/attendants", [
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'promotion_code_id' => $promotion_code->id,
        ]);
        $reschedule = $schedule->users();
        $response->assertOk($reschedule);
    }

    public function test_purchase()
    {
        $schedule = Schedule::factory()->create();
        $user = User::factory()->create();
        $this->json('post', "api/schedules/{$schedule->id}/purchase");
        $schedule->users()->attach($user->id);
    }

    public function test_promo_code()
    {
        $schedule = Schedule::factory()->create();
        $promotion = Promotion::factory()->create();
        $service = Service::factory()->create(['id' => $schedule->service_id]);
        $discipline = Discipline::factory()->create();
        $serviceType = ServiceType::factory()->create();
        $focusArea = FocusArea::factory()->create();
        $service->disciplines()->attach($discipline);
        $service->service_types()->attach($serviceType);
        $service->focus_areas()->attach($focusArea);
        $promoCode = PromotionCode::factory()->create(['promotion_id' => $promotion->id]);

        $response = $this->json('post', "api/schedules/{$schedule->id}/promoсodes", ['promo_code' => $promoCode->name]);
        $response->assertOk();
    }

    public function test_validate_request_class_ad_hoc_schedule(): void
    {
        $schedule = Schedule::factory()->make();
        $service = Service::factory()->create(['service_type_id' => 'class_ad_hoc']);
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertJsonFragment($schedule->prices->toArray());
        $response->assertOk();

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_workshop_schedule(): void
    {
        $schedule = Schedule::factory()->make();
        $service = Service::factory()->create(['service_type_id' => 'workshop']);
        $price = Price::factory()->create();
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_econtent_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'econtent']);
        $schedule = Schedule::factory()->make();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_class_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'class']);
        $schedule = Schedule::factory()->make();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_cources_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'cources']);
        $schedule = Schedule::factory()->make();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_events_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'events']);
        $schedule = Schedule::factory()->make();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_product_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'product']);
        $schedule = Schedule::factory()->make();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_retreat_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'retreat']);
        $schedule = Schedule::factory()->make();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_validate_request_raining_program_schedule(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'training_program']);
        $schedule = Schedule::factory()->make();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(1, $schedule->prices);
    }

    public function test_saving_apointment_schedule_with_relations(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'appointment']);
        $schedule = Schedule::factory()->make();
        $scheduleUnavailabilities = ScheduleUnavailabilities::factory()->count(2)->create();
        $scheduleAvailabilities = ScheduleAvailabilities::factory()->count(2)->create();
        $price = Price::factory()->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'schedule_unavailabilities' => $scheduleUnavailabilities->pluck('id'),
            'schedule_availabilities' => $scheduleAvailabilities->pluck('id'),
            'prices' => $price->pluck('id')
        ]);
        $response->assertOk();
        $response->assertJsonFragment($schedule->prices->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(2, $schedule->schedule_unavailabilities);
        self::assertCount(2, $schedule->schedule_availabilities);
        self::assertCount(1, $schedule->prices);
    }

    public function test_saving_schedule_files_relationships_schedules(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'appointment']);
        $schedule = Schedule::factory()->make();
        $scheduleFiles = ScheduleFile::factory()->count(3)->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'schedule_files' => $scheduleFiles->pluck('id'),
        ]);

        $response->assertOk();
        $response->assertJsonFragment($schedule->schedule_files->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(3, $schedule->schedule_files);
    }

    public function test_saving_schedule_hidden_files_relationships_schedules(): void
    {
        $service = Service::factory()->create(['service_type_id' => 'appointment']);
        $schedule = Schedule::factory()->make();
        $scheduleHiddenFiles = ScheduleHiddenFile::factory()->count(3)->create();

        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'schedule_hidden_files' => $scheduleHiddenFiles->pluck('id'),
        ]);

        $response->assertOk();
        $response->assertJsonFragment($schedule->schedule_hidden_files->toArray());

        $schedule = Schedule::find($response->getOriginalContent()->id);
        self::assertCount(3, $schedule->schedule_hidden_files);
    }
}
