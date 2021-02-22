<?php

namespace Tests\Api;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BookingMyClientsTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_practitioner_can_see_his_clients(): void
    {
        $myClients    = User::factory()->count(5)->create();
        $notMyClient = User::factory()->create();

        $practitioner      = $this->user;
        $otherPractitioner = User::factory()->create();

        //my services
        $service  = Service::factory()->create(['user_id' => $practitioner->id]);
        $schedule = Schedule::factory()->create(['service_id' => $service->id]);
        foreach($myClients as $myClient){
            Booking::factory()->count(3)->create(['user_id' => $myClient->id, 'schedule_id' => $schedule->id, 'datetime_from' => Carbon::now()->addDays(1)]);
            Booking::factory()->count(3)->create(['user_id' => $myClient->id, 'schedule_id' => $schedule->id, 'datetime_from' => Carbon::now()->subDays(1)]);
        }

        //not my services
        $notMyService = Service::factory()->create(['user_id' => $otherPractitioner->id]);
        $notMySchedule     = Schedule::factory()->create(['service_id' => $notMyService->id]);
        Booking::factory()->count(5)->create(['user_id' => $notMyClient->id, 'schedule_id' => $notMySchedule->id]);

        $response = $this->json('get', '/api/bookings/my-clients');
        $response->assertOk();
    }
}
