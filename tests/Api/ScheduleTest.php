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

    public function test_store_schedule(): void
    {
        $service = factory(Service::class)->create();
        $schedule = factory(Schedule::class)->create();
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
        $schedule = factory(Schedule::class)->create();
        $user = factory(User::class)->create();
        $promotion_code = factory(PromotionCode::class)->create();
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
        $schedule = factory(Schedule::class)->create();
        $user = factory(User::class)->create();
        $response = $this->json('post', "api/schedules/{$schedule->id}/purchase");
        $schedule->users()->attach($user->id);
    }
}
