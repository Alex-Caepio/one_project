<?php

namespace Tests\Api;

use App\Models\PromotionCode;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

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
        $response = $this->json('get', "/api/services/{$service->id}/schedule");

        $response
            ->assertOk();
    }
    public function test_store_schedule(): void
    {
        $service = Service::factory()->create();
        $schedule = Schedule::factory()->create();
        $response = $this->json('post', "api/services/{$service->id}/schedule", [
            'title' => $schedule->title,
            'service_id' => $service->id,
            'start_date' => $schedule->start_date,
            'end_date' => $schedule->end_date,
            'cost' => $schedule->cost,
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
        $response = $this->json('post', "api/schedules/{$schedule->id}/purchase");
        $schedule->users()->attach($user->id);
    }
    public function test_promo_code()
    {
        $schedule = Schedule::factory()->create();
        $response = $this->json('post', "api/schedule/{$schedule->id}/promoсode");
        $response->assertOk();
    }
}
