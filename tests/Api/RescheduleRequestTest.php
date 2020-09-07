<?php

namespace Tests\Api;

use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RescheduleRequestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_store_reschedule_request()
    {
        $schedule = factory(Schedule::class)->create();
        $reschedule = factory(RescheduleRequest::class)->create();
        $response = $this->json('post', "api/schedule/{$schedule->id}/reschedule", [
            'user_id' => $reschedule->user_id,
            'schedule_id' => $schedule->id,
            'new_schedule_id' => $reschedule->new_schedule_id,
        ]);
        $response->assertOk();
    }

    public function test_accept_reschedule_request()
    {
        $user = factory(User::class)->create();
        $reschedule = factory(RescheduleRequest::class)->create();
        $response = $this->json('post', "api/reschedule-requests/{$reschedule->id}/accept");
        $user->schedules()->detach($reschedule);
        $user->schedules()->attach($reschedule);
    }

    public function test_decline_reschedule_request()
    {
        $reschedule = factory(RescheduleRequest::class)->create();
        $response = $this->json('delete', "api/reschedule-requests/{$reschedule->id}/decline");
        $response->assertStatus(204);
    }
}
