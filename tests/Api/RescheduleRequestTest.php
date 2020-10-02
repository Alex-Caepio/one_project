<?php

namespace Tests\Api;

use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RescheduleRequestTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }
    public function test_can_get_all_reschedule_request(): void
    {

        RescheduleRequest::factory()->count(2)->create();
        $response = $this->json('get', "/api/reschedule-requests");

        $response
            ->assertOk();
    }
    public function test_store_reschedule_request()
    {
        $schedule = Schedule::factory()->create();
        $reschedule = RescheduleRequest::factory()->create();
        $response = $this->json('post', "api/schedules/{$schedule->id}/reschedule", [
            'user_id' => $reschedule->user_id,
            'schedule_id' => $schedule->id,
            'new_schedule_id' => $reschedule->new_schedule_id,
        ]);
        $response->assertOk();
    }

    public function test_accept_reschedule_request()
    {
        $user = User::factory()->create();
        $reschedule = RescheduleRequest::factory()->create();
        $response = $this->json('post', "api/reschedule-requests/{$reschedule->id}/accept");
        $user->schedules()->detach($reschedule);
        $user->schedules()->attach($reschedule);
    }

    public function test_decline_reschedule_request()
    {
        $reschedule = RescheduleRequest::factory()->create();
        $response = $this->json('delete', "api/reschedule-requests/{$reschedule->id}/decline");
        $response->assertStatus(204);
    }
}
