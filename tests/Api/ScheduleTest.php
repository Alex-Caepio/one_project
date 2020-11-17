<?php

namespace Tests\Api;

use App\Models\Discipline;
use App\Models\FocusArea;
use App\Models\Promotion;
use App\Models\PromotionCode;
use App\Models\Schedule;
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

    public function test_validate_request_schedule(): void
    {
        $schedule = Schedule::factory()->make();

        $service = Service::factory()->create(['service_type_id' => 'class_ad_hoc']);
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
        ]);
        $response->assertOk();

        $service = Service::factory()->create(['service_type_id' => 'workshop']);
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
        ]);
        $response->assertOk();

        $service = Service::factory()->create(['service_type_id' => 'econtent']);
        $schedule = Schedule::factory()->make();
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
        ]);
        $response->assertOk();

        $service = Service::factory()->create(['service_type_id' => 'class']);
        $schedule = Schedule::factory()->make();
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
        ]);
        $response->assertOk();

        $service = Service::factory()->create(['service_type_id' => 'class_ad_hoc']);
        $schedule = Schedule::factory()->make();
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
        ]);
        $response->assertOk();

        $service = Service::factory()->create(['service_type_id' => 'cource_program']);
        $schedule = Schedule::factory()->make();
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
        ]);
        $response->assertOk();

        $service = Service::factory()->create(['service_type_id' => 'event']);
        $schedule = Schedule::factory()->make();
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
        ]);
        $response->assertOk();

        $service = Service::factory()->create(['service_type_id' => 'product']);
        $schedule = Schedule::factory()->make();
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
        ]);
        $response->assertOk();

        $service = Service::factory()->create(['service_type_id' => 'class_ad_hoc']);
        $schedule = Schedule::factory()->make();
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
        ]);
        $response->assertOk();

        $service = Service::factory()->create(['service_type_id' => 'retreat']);
        $schedule = Schedule::factory()->make();
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
        ]);
        $response->assertOk();

        $service = Service::factory()->create(['service_type_id' => 'training_program']);
        $schedule = Schedule::factory()->make();
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
        ]);
        $response->assertOk();

        $service = Service::factory()->create(['service_type_id' => 'purchase']);
        $schedule = Schedule::factory()->make();
        $response = $this->json('post', "api/services/{$service->id}/schedules", [
            'title' => $schedule->title,
            'service_id' => $service->id,
        ]);
        $response->assertOk();
    }

}
