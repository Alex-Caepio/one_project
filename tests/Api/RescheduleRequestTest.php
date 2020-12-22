<?php

namespace Tests\Api;

use App\Models\Booking;
use App\Models\RescheduleRequest;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
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
        $schedule   = Schedule::factory()->create();
        $reschedule = RescheduleRequest::factory()->create();
        $response   = $this->json('post', "api/schedules/{$schedule->id}/reschedule", [
            'user_id'         => $reschedule->user_id,
            'schedule_id'     => $schedule->id,
            'new_schedule_id' => $reschedule->new_schedule_id,
        ]);
        $response->assertOk();
    }

    public function test_accept_reschedule_request()
    {
        $user       = User::factory()->create();
        $reschedule = RescheduleRequest::factory()->create();
        $response   = $this->json('post', "api/reschedule-requests/{$reschedule->id}/accept");
        $user->schedules()->detach($reschedule);
        $user->schedules()->attach($reschedule);
    }

    public function test_decline_reschedule_request()
    {
        $reschedule = RescheduleRequest::factory()->create();
        $response   = $this->json('post', "api/reschedule-requests/{$reschedule->id}/decline");
        $response->assertStatus(204);
    }

    public function test_on_location_change_correct_reschedule_is_created()
    {
        Event::fake();
        $service  = Service::factory()->create();
        $schedule = Schedule::factory()->create([
            'service_id'         => $service->id,
            'location_displayed' => 'city'
        ]);
        Booking::factory()->create(['schedule_id' => $schedule->id]);

        $this->json('put', "api/schedules/{$schedule->id}", [
            'location_displayed' => '123asd',
        ])->assertOk();

        $this->assertDatabaseHas('reschedule_requests', [
            'old_location_displayed' => 'city',
            'new_location_displayed' => '123asd',
        ]);
    }

    public function test_on_date_change_correct_reschedule_is_created()
    {
        Event::fake();
        $service  = Service::factory()->create();
        $schedule = Schedule::factory()->create([
            'service_id' => $service->id,
            'start_date' => '2020-11-20 12:38:31',
            'end_date'   => '2021-12-01 13:38:31'
        ]);
        Booking::factory()->create(['schedule_id' => $schedule->id]);

        $response = $this->json('put', "api/schedules/{$schedule->id}", [
            'start_date' => '2020-12-20 17:38:31',
            'end_date'   => '2021-01-01 17:38:31'
        ]);
        $response->assertOk();

        $this->assertDatabaseHas('reschedule_requests', [
            'old_start_date' => '2020-11-20 12:38:31',
            'old_end_date'   => '2021-12-01 13:38:31',
            'new_start_date' => '2020-12-20 17:38:31',
            'new_end_date'   => '2021-01-01 17:38:31'
        ]);
    }
}
